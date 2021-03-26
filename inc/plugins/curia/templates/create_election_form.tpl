<form action="misc.php?action=curia_create_election" method="post" enctype="multipart/form-data" name="input">
    <table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
        <tr>
            <td class="thead" colspan="2">
                <strong>{$lang->curia_create_election}</strong>
            </td>
        </tr>
        <tr>
            <td class="tcat" colspan="2">
                <strong>{$lang->curia_create_election_props}</strong>
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_props_title}
            </td>
            <td class="trow1">
                <input type="text" class="textbox" name="title">
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_props_daterange}<br>
                <span class="smalltext">{$lang->curia_create_election_props_daterange_desc}</span>
            </td>
            <td class="trow1">
                <div id="daterange">
                    <input type="text" class="textbox" name="startdate" id="daterange_start">
                    <span>@</span>
                    <input type="text" class="textbox" name="starttime" placeholder="08:00">
                    <span>-</span>
                    <input type="text" class="textbox" name="enddate" id="daterange_end">
                    <span>@</span>
                    <input type="text" class="textbox" name="endtime" placeholder="16:00">
                </div>
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_props_methodology}
            </td>
            <td class="trow1">
                <select name="methodology">
                    <option value="condorcet">{$lang->curia_methods_condorcet}</option>
                    <option value="borda">{$lang->curia_methods_borda}</option>
                    <option value="dowdall">{$lang->curia_methods_dowdall}</option>
                    <option value="copeland">{$lang->curia_methods_copeland}</option>
                    <option value="dodgsonquick">{$lang->curia_methods_dodgesonquick}</option>
                    <option value="dodgesontideman">{$lang->curia_methods_dodgesontideman}</option>
                    <option value="instantrunoff">{$lang->curia_methods_instantrunoff}</option>
                    <option value="kemenyyoung">{$lang->curia_methods_kemenyyoung}</option>
                    <option value="fptp">{$lang->curia_methods_fptp}</option>
                    <option value="tworound">{$lang->curia_methods_tworound}</option>
                    <option value="minimaxwinning">{$lang->curia_methods_minimaxwinning}</option>
                    <option value="minimaxmargin">{$lang->curia_methods_minimaxmargin}</option>
                    <option value="minimaxopposition">{$lang->curia_methods_minimaxopposition}</option>
                    <option value="rankedpairsmargin">{$lang->curia_methods_rankedpairsmargin}</option>
                    <option value="rankedpairswinning">{$lang->curia_methods_rankedpairswinning}</option>
                    <option value="shulze">{$lang->curia_method_schulze}</option>
                    <option value="schulzemargin">{$lang->curia_method_schulzemargin}</option>
                    <option value="schulzeratio">{$lang->curia_method_schulzeratio}</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_props_desc}
            </td>
            <td class="trow1">
                <textarea type="text" class="textarea" name="description"></textarea>
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_props_candidates}
            </td>
            <td class="trow1">
                <textarea type="text" class="textarea" name="candidates"></textarea>
            </td>
        </tr>
        <tr>
            <td class="tcat" colspan="2">
                <strong>{$lang->curia_create_election_perms}</strong>
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_perms_public}
            </td>
            <td class="trow1">
                <input type="checkbox" class="checkbox" name="perm_editing"> {$lang->curia_create_election_perms_public_desc}
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_perms_private}
            </td>
            <td class="trow1">
                <input type="checkbox" class="checkbox" name="perm_private"> {$lang->curia_create_election_perms_private_desc}
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_perms_anonymous}
            </td>
            <td class="trow1">
                <input type="checkbox" class="checkbox" name="perm_anonymous"> {$lang->curia_create_election_perms_anonymous_desc}
            </td>
        </tr>
        <tr>
            <td class="trow1">
                {$lang->curia_create_election_perms_editing}
            </td>
            <td class="trow1">
                <input type="checkbox" class="checkbox" name="perm_editing"> {$lang->curia_create_election_perms_editing_desc}
            </td>
        </tr>
    </table>
    <br />
    <div style="text-align:center"><input type="submit" class="button" name="submit" value="{$lang->curia_create}"
            tabindex="4" accesskey="s" /></div>
    <input type="hidden" name="run" value="create" />
</form>
<script>
const elem = document.getElementById('daterange');
const start = document.getElementById('daterange_start');
const end = document.getElementById('daterange_end');
const rangepicker = new DateRangePicker(elem, {
    minDate: new Date().setHours(0, 0, 0, 0),
    inputs: [start, end]
});
</script>
