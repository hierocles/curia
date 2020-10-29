<?php
/*
    Part of FTPT method Module - From the original Condorcet PHP

    Condorcet PHP - Election manager and results calculator.
    Designed for the Condorcet method. Integrating a large number of algorithms extending Condorcet. Expandable for all types of voting systems.

    By Julien Boudry and contributors - MIT LICENSE (Please read LICENSE.txt)
    https://github.com/julien-boudry/Condorcet
*/
declare(strict_types=1);

namespace CondorcetPHP\Condorcet\Algo\Methods\Majority;

use CondorcetPHP\Condorcet\Result;
use CondorcetPHP\Condorcet\Algo\{Method, MethodInterface};

abstract class Majority_Core extends Method implements MethodInterface
{
    protected array $_admittedCandidates = [];
    protected ?array $_Stats = null;

    protected function getStats(): array
    {
        $stats = [];

        foreach ($this->_Stats as $roundNumber => $roundScore) :
            foreach ($roundScore as $candidateKey => $oneScore) :
                $stats[$roundNumber][(string) $this->_selfElection->getCandidateObjectFromKey($candidateKey)] = $oneScore;
            endforeach;
        endforeach;

        return $stats;
    }


/////////// COMPUTE ///////////

    protected function compute(): void
    {
        $round = 1;
        $resolved = false;
        $score = [];

        // Start a round
        while ($resolved === false) :
            $roundScore = $this->doOneRound();
            \arsort($roundScore, \SORT_NUMERIC);

            $score[$round] = $roundScore;

            if ($round === 1) :
                foreach ($this->_selfElection->getCandidatesList() as $oneCandidate) :
                    $score[$round][$this->_selfElection->getCandidateKey($oneCandidate)] ??= 0;
                endforeach;
            endif;

            if ( $round === static::MAX_ROUND || \reset($roundScore) > (\array_sum($roundScore) / 2) ) :
                $resolved = true;

                if ( isset($score[$round - 1]) && $score[$round] === $score[$round - 1] ) :
                    unset($score[$round]);
                endif;
            else :
                $lastScore = null;
                $nextRoundAddedCandidates = 0;

                $this->_admittedCandidates = [];

                foreach ($roundScore as $oneCandidateKey => $oneScore) :
                    if ($lastScore === null ||
                        $nextRoundAddedCandidates < ( static::TARGET_NUMBER_OF_CANDIDATES_FOR_THE_NEXT_ROUND + (static::CHANGING_THE_NUMBER_OF_TARGETED_CANDIDATES_AFTER_EACH_ROUND * ($round - 1)) ) ||
                        $oneScore === $lastScore
                        ) :
                            $this->_admittedCandidates[] = $oneCandidateKey;
                            $lastScore = $oneScore;
                            $nextRoundAddedCandidates++;
                    endif;
                endforeach;
            endif;

            $round++;
        endwhile;

        // Compute Ranking
        $rank = 0;
        $result = [];
        \krsort($score, \SORT_NUMERIC);
        $doneCandidates = [];

        foreach ($score as $oneRound) :
            $lastScore = null;
            foreach ($oneRound as $candidateKey => $candidateScore) :
                if (!\in_array($candidateKey, $doneCandidates, true)) :
                    if ($candidateScore === $lastScore) :
                        $doneCandidates[] = $result[$rank][] = $candidateKey;
                    else :
                        $result[++$rank] = [$doneCandidates[] = $candidateKey];
                        $lastScore = $candidateScore;
                    endif;
                endif;
            endforeach;
        endforeach;

        // Finalizing
        \ksort($score, \SORT_NUMERIC);
        $this->_Stats = $score;
        $this->_Result = $this->createResult($result);
    }

    protected function doOneRound () : array
    {
        $roundScore = [];

        foreach ($this->_selfElection->getVotesManager()->getVotesValidUnderConstraintGenerator() as $oneVote) :

            $weight = $this->_selfElection->isVoteWeightAllowed() ? $oneVote->getWeight() : 1;

            $oneRanking = $oneVote->getContextualRanking($this->_selfElection);

            if ( !empty($this->_admittedCandidates) ) :
                foreach ($oneRanking as $rankKey => $oneRank) :
                    foreach ($oneRank as $InRankKey => $oneCandidate) :
                        if ( !\in_array($this->_selfElection->getCandidateKey($oneCandidate), $this->_admittedCandidates, true) ) :
                            unset($oneRanking[$rankKey][$InRankKey]);
                        endif;
                    endforeach;

                    if (empty($oneRanking[$rankKey])) :
                        unset($oneRanking[$rankKey]);
                    endif;
                endforeach;

                if( ($newFirstRank = \reset($oneRanking)) !== false ) :
                    $oneRanking = [1 => $newFirstRank];
                else :
                    continue;
                endif;
            endif;

            if (isset($oneRanking[1])) :
                foreach ($oneRanking[1] as $oneCandidateInRank) :
                    $roundScore[$this->_selfElection->getCandidateKey($oneCandidateInRank)] ??= 0;
                    $roundScore[$this->_selfElection->getCandidateKey($oneCandidateInRank)] += (1 / \count($oneRanking[1])) * $weight;
                endforeach;
            endif;
        endforeach;

        return $roundScore;
    }
}
