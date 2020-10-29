<?php
declare(strict_types=1);

namespace CondorcetPHP\Condorcet\Tests\Console\Commands;

use PHPUnit\Framework\TestCase;
use CondorcetPHP\Condorcet\Console\CondorcetApplication;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;


class ElectionCommandTest extends TestCase
{
    private CommandTester $electionCommand;

    public function setUp() : void
    {
        CondorcetApplication::create();

        $this->electionCommand = new CommandTester(CondorcetApplication::$SymfonyConsoleApplication->find('election'));
    }

    public function testConsoleSimpleElection () : void
    {
        $this->electionCommand->execute([
                                            '--candidates' => 'A;B;C',
                                            '--votes' => 'A>B>C;C>B>A;B>A>C',
                                            '--stats' => null,
                                            '--natural-condorcet' => null,
                                            '--allows-votes-weight' => null,
                                            '--no-tie' => null,
                                            '--list-votes' => null,
                                            '--desactivate-implicit-ranking' => null,
                                            '--show-pairwise' => null
                                        ],[
                                            'verbosity' => OutputInterface::VERBOSITY_VERBOSE
                                        ]
        );

        $output = $this->electionCommand->getDisplay();
        // \var_dump($output);

        self::assertStringContainsString('3 Candidates(s) Registered  ||  3 Vote(s) Registered', $output);

        self::assertStringContainsString('Schulze', $output);
        self::assertStringContainsString('Registered Candidates', $output);
        self::assertStringContainsString('Stats - Votes Registration', $output);
        self::assertStringContainsString('Pairwise', $output);
        self::assertStringContainsString('Stats:', $output);
        self::assertStringContainsString('Votes List', $output);

        self::assertMatchesRegularExpression('/Is vote weight allowed\?( )+TRUE/', $output);
        self::assertMatchesRegularExpression('/Votes are evaluated according to the implicit ranking rule\?( )+FALSE./', $output);
        self::assertMatchesRegularExpression('/Is vote tie in rank allowed\?( )+TRUE/', $output);

        self::assertStringContainsString('[OK] Success', $output);
    }

    public function testConsoleAllMethodsArgument () : void
    {
        $this->electionCommand->execute([
                                            '--candidates' => 'A;B;C',
                                            '--votes' => 'A>B>C;C>B>A;B>A>C',

                                            'methods' => ['all']
        ]);

        $output = $this->electionCommand->getDisplay();
        // \var_dump($output);

        self::assertStringContainsString('Copeland', $output);

        self::assertStringContainsString('[OK] Success', $output);
    }

    public function testConsoleMultiplesMethods () : void
    {
        $this->electionCommand->execute([
                                            '--candidates' => 'A;B;C',
                                            '--votes' => 'A>B>C;C>B>A;B>A>C',

                                            'methods' => ['Copeland', 'RankedPairs', 'Minimax']
        ]);

        $output = $this->electionCommand->getDisplay();
        // \var_dump($output);

        self::assertStringContainsString('Copeland', $output);
        self::assertStringContainsString('Ranked Pairs M', $output);
        self::assertStringContainsString('Minimax Winning', $output);

        self::assertStringContainsString('[OK] Success', $output);
    }

    public function testConsoleFileInput () : void
    {
        $this->electionCommand->execute([
            '--candidates' => __DIR__.'/data.candidates',
            '--votes' => __DIR__.'/data.votes'
        ]);

        $output = $this->electionCommand->getDisplay();
        // \var_dump($output);

        self::assertStringContainsString('Schulze', $output);
        self::assertStringContainsString('A,B', $output);
        self::assertStringContainsString('C#', $output);
    }

    public function testInteractiveCommand () : void
    {
        $this->electionCommand->setInputs([
            'A',
            'B',
            'C',
            '',
            'A>B>C',
            'B>A>C',
            'A>C>B',
            ''
            ]);

        $this->electionCommand->execute([
            'command' => 'election'
        ]);

        $output = $this->electionCommand->getDisplay();
        // \var_dump($output);

        self::assertStringContainsString('Results: Schulze Winning', $output);
    }

    public function testNonInteractionMode () : void
    {
        self::expectException(\CondorcetPHP\Condorcet\Throwable\CondorcetException::class);
        self::expectExceptionCode(6);

        $this->electionCommand->execute([],['interactive' => false]);

        // $output = $this->electionCommand->getDisplay();
        // \var_dump($output);
    }

    public function testCustomizeVotesPerMb () : void
    {
        $this->electionCommand->execute([
                                            '--candidates' => 'A;B;C',
                                            '--votes' => 'A>B>C;C>B>A;B>A>C',
                                            '--votes-per-mb' => 42
                                        ]);

        self::assertSame(42, \CondorcetPHP\Condorcet\Console\Commands\ElectionCommand::$VotesPerMB);

        // $output = $this->electionCommand->getDisplay();
        // \var_dump($output);
    }
}
