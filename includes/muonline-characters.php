<?php
function muonline_get_account_id($user_id) {
    $response = call_api('GET', '/account?LoginName=eq.' . urlencode($user_id));
    $response_data = json_decode($response, true); // Add this line to decode the JSON response
    return $response_data[0]['Id'];
}

function muonline_check_characters($account_id) {
    $response = call_api('GET', '/character?AccountId=eq.' . urlencode($account_id) . '&select=*,characterclass(*)');
    $response_data = json_decode($response, true); // Decode the JSON response
    return $response_data;
}

function muonline_check_attribute_names() {
    $response = call_api('GET', '/attributedefinition');
    $response_data = json_decode($response, true); // Decode the JSON response
    return $response_data;
}
function muonline_check_characters_attribute($character_id, $attribute_names) {
    $response = call_api('GET', '/statattribute?CharacterId=eq.' . urlencode($character_id));
    $response_data = json_decode($response, true); // Decode the JSON response

    $character_attributes = [];
    foreach ($response_data as $attribute) {
        $attribute_id = $attribute['DefinitionId'];
        if (isset($attribute_names[$attribute_id])) {
            $character_attributes[$attribute_names[$attribute_id]] = $attribute['Value'];
        }
    }

    return $character_attributes;
}
function muonline_get_character_zen($inventory_id) {
    $response = call_api('GET', '/itemstorage?Id=eq.' . urlencode($inventory_id));
    $response_data = json_decode($response, true); // Decode the JSON response

    if (isset($response_data[0]['Money'])) {
        return $response_data[0]['Money'];
    } else {
        return 0;
    }
}



function muonline_show_characters($atts) {
    // Check if the user is logged in
    $logged_in_user = muonline_is_user_logged_in();

    ob_start();

    if ($logged_in_user) {
        // Get AccountId
        $account_id = muonline_get_account_id($logged_in_user);

        // Get character data
        $character_data = muonline_check_characters($account_id);
        
        // Fetch attribute names
        $attribute_names = muonline_check_attribute_names();

        // Create a mapping of attribute IDs to their names
        $attribute_name_map = [];
        foreach ($attribute_names as $attribute) {
            $attribute_name_map[$attribute['Id']] = $attribute['Designation'];
        }
        
        // Check if a character name is in the URL
        $charname = isset($_GET['charname']) ? $_GET['charname'] : '';

        // Display the logged-in user content
        if ($charname) {
            // Find the character with the given name
            $character = null;
            foreach ($character_data as $c) {
                if ($c['Name'] == $charname) {
                    $character = $c;
                    break;
                }
            }

            if ($character) {
                // Get character attributes
                $character_attributes = muonline_check_characters_attribute($character['Id'], $attribute_name_map);

                // Get character Zen
                $zen_value = muonline_get_character_zen($character['InventoryId']);
                
                // Show custom HTML for the character
                ?>
                <div><h1 align="center"><?php echo $character['Name']; ?></h1></div>
            </br>
                <table border="0" class="sblock" width="100%" id="over_sh">
<tbody><tr>
    <td align="center" class="chatp fill" colspan="3"><div class="title">General information</div></td>
</tr>
<tr>
    <td align="center" valign="top" class="sblock" style="width: 20%;" rowspan="22">

        <table align="center" border="0" width="100%" height="324">
        <tbody><tr>
            <?php
        function get_initials($name) {
    $words = explode(" ", $name);
    $initials = "";

    foreach ($words as $w) {
        $initials .= $w[0];
    }

    return strtolower($initials);
}
$imageName = get_initials($character['characterclass']['Name']) . '.jpg';

    $stateText = 'New';  // default value
    if ($character['State'] == 1) {
        $stateText = 'Hero';
    } else if ($character['State'] == 2) {
        $stateText = 'Light Hero';
    }
    else if ($character['State'] == 3) {
        $stateText = 'Normal';
    }
    else if ($character['State'] == 4) {
        $stateText = 'Player Kill';
    }
    else if ($character['State'] == 5) {
        $stateText = 'Player Kill 1st Stage';
    }
    else if ($character['State'] == 6) {
        $stateText = 'Player Kill 2nd Stage';
    }

?>

<td align="center" valign="middle" class="sblock" colspan="2" height="223">
    <img src="/images/class/<?php echo $imageName; ?>" title="<?php echo $character['characterclass']['Name']; ?>">
</td>
        </tr>
        <tr>
            <td align="center" class="sblock" colspan="2" height="25"><div class="title"><b>Guild</b></div></td>
        </tr>
        <tr>
            <td align="left" class="sblock">Guild</td>
            <td align="left" class="sblock"><div class="title"><a href="en/index.php?page=info&amp;act=getguild&amp;name=TheKings&amp;serv=server5" class="toolinited">TheKings</a></div></td>
        </tr>
        <tr>
            <td align="left" class="sblock">Master</td>
            <td align="left" class="sblock"><div class="title"><a href="en/index.php?page=info&amp;act=getchar&amp;name=-DarkSide-&amp;serv=server5" class="toolinited">-DarkSide-</a></div></td>
        </tr>
        <tr>
            <td align="left" class="sblock">Guild Buff</td>
            <td align="left" class="sblock"><div class="title">9,049</div></td>
        </tr>
        </tbody></table>
    </td>
    <td align="left" class="sblock" style="width: 40%;">Character</td>
    <td align="left" class="sblock" style="width: 40%;"><div class="title"><?php echo $character['Name']; ?> <img width="10" height="10" src="style/images/vip.png"></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Class</td>
    <td align="left" class="sblock"><div class="title"><?php echo $character['characterclass']['Name']; ?></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Level</td>
    <td align="left" class="sblock"><div class="title"><?php echo $character_attributes['Level']; ?></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Resets</td>
    <td align="left" class="sblock"><div class="title"><?php echo $character_attributes['Resets']; ?></div></td>
</tr>
<tr>
    <td align="left" class="sblock">PK level</td><td align="left" class="sblock"><div class="title"><?php echo $stateText; ?> (<?php echo $character['State']; ?>)</div>
</td>
</tr>
<tr>
    <td align="left" class="sblock">Location</td><td align="left" class="sblock"><div class="title">Lorencia (<?php echo $character['PositionX']; ?> x <?php echo $character['PositionY']; ?>)</div></td>
</tr>
<tr>
    <td align="left" class="sblock">Strength</td><td align="left" class="sblock"><div class="title"><a style="cursor:help" class="toolinited"><?php echo $character_attributes['Base Strength']; ?></a></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Agility</td><td align="left" class="sblock"><div class="title"><a style="cursor:help" class="toolinited"><?php echo $character_attributes['Base Agility']; ?></a></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Vitality</td><td align="left" class="sblock"><div class="title"><a style="cursor:help" class="toolinited"><?php echo $character_attributes['Base Vitality']; ?></a></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Energy</td><td align="left" class="sblock"><div class="title"><a style="cursor:help" class="toolinited"><?php echo $character_attributes['Base Energy']; ?></a></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Command</td><td align="left" class="sblock"><div class="title"><a style="cursor:help" class="toolinited"><?php echo $character_attributes['Command']; ?></a></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Bought stats</td><td align="left" class="sblock"><div class="title">2000</div></td>
</tr>
<tr>
    <td align="left" class="sblock">Achiev. points</td><td align="left" class="sblock"><div class="title"><?php echo $character_attributes['Points per Level up']; ?></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Buff level</td><td align="left" class="sblock"><div class="title"><?php echo $character_attributes['Master points per master Level up']; ?></div></td>
</tr>
<tr>
    <td align="left" class="sblock">Equipment</td><td align="left" class="sblock"><div class="title">0</div></td>
</tr>
<tr>
    <td align="left" class="sblock">Completed Quests</td><td align="left" class="sblock"><div class="title">380</div></td>
</tr>
<tr>
    <td align="left" class="sblock">RQuest stats</td><td align="left" class="sblock"><div class="title">438</div></td>
</tr>
<tr>
    <td align="left" class="sblock">Server</td><td align="left" class="sblock"><div class="title2">Extreme-GS</div></td>
</tr>
<tr>
    <td align="left" class="sblock">Status</td><td align="left" class="sblock"><div class="title"><font color="red">Offline</font></div></td>
</tr>
</tbody></table>
<table align="center" class="sblock" width="100%">
    <tbody><tr>
        <td align="center" class="chatp fill" onclick="if (!window.__cfRLUnblockHandlers) return false; openInvent('invent');" onmouseover="if (!window.__cfRLUnblockHandlers) return false; this.style.cursor = 'pointer'" colspan="3" style="cursor: pointer;"><div class="title">Equipment</div></td>
    </tr>
    </tbody></table>

<div style="display: block; overflow: hidden;" id="invent" class="sblocki">
     <div id="over">

          <table align="center" width="100%" border="0" class="sblock" cellpadding="0" cellspacing="0">
          <tbody><tr>
              <td height="5" colspan="11"></td>
          </tr>
          <tr>
              <td width="5" height="80"></td>
              <td class="sblock3" width="80"><img src="../images/items/13-67.gif" alt="" class="toolinited"></td>
              <td width="5"></td>
              <td width="25"></td>
              <td width="5"></td>
              <td class="sblock3" width="80"><img src="../images/items/7-9.gif" alt="" class="toolinited"></td>
              <td width="5"></td>
              <td class="sblock3" width="115" colspan="3"><img src="../images/items/12-5.gif" alt="" class="toolinited"></td>
              <td width="5"></td>
          </tr>
          <tr>
              <td height="5" colspan="11"></td>
          </tr>
          <tr>
              <td width="5" height="150" rowspan="2"></td>
              <td class="sblock3" width="80" rowspan="2"><img src="../images/items/0-75.png" alt="" class="toolinited"></td>
              <td width="5" rowspan="2"></td>
              <td class="sblock3" width="25" height="25"><img src="../images/items/13-13.gif" alt="" class="toolinited"></td>
              <td width="5" rowspan="2"></td>
              <td class="sblock3" width="80" rowspan="2"><img src="../images/items/8-9.gif" alt="" class="toolinited"></td>
              <td width="5" rowspan="2"></td>
              <td width="25" rowspan="2"></td>
              <td width="5" rowspan="2"></td>
              <td class="sblock3" width="80" rowspan="2"><img src="../images/items/6-10.gif" alt="" class="toolinited"></td>
              <td width="5" rowspan="2"></td>
             </tr>
             <tr>
                 <td height="125"></td>
             </tr>
             <tr>
                 <td height="5" colspan="11"></td>
             </tr>
             <tr>
                 <td width="5" height="80" rowspan="2"></td>
                 <td class="sblock3" width="80" rowspan="2"><img src="../images/items/10-9.gif" alt="" class="toolinited"></td>
                 <td width="5" rowspan="2"></td>
                 <td class="sblock3" width="25" height="25"><img src="../images/items/13-24.gif" alt="" class="toolinited"></td>
                 <td width="5" rowspan="2"></td>
                 <td class="sblock3" width="80" rowspan="2"><img src="../images/items/9-9.gif" alt="" class="toolinited"></td>
                 <td width="5" rowspan="2"></td>
                 <td class="sblock3" width="25" height="25"><img src="../images/items/13-24.gif" alt="" class="toolinited"></td>
                 <td width="5" rowspan="2"></td>
                 <td class="sblock3" width="80" rowspan="2"><img src="../images/items/11-9.gif" alt="" class="toolinited"></td>
                 <td width="5" rowspan="2"></td>
             </tr>
             <tr>
                 <td height="55"></td><td height="55"></td>
             </tr>
             <tr>
                 <td height="5" colspan="11"></td>
             </tr>
             </tbody></table>

     </div>
</div>
                <?php
            } else {
                // Character not found
                ?>
                <div class="warning-summary ">Character not found</div>
                <?php
            }
        } else {
            // Show all characters
            ?>
<table class="sblock" width="100%">
<tbody><tr>
    <td align="center" class="chatp fill" colspan="4"><div class="title">Your characters</div></td>
</tr>
<tr>
    <td align="center" class="sblock2"><div class="title"><b>Character</b></div></td>
    <td align="center" class="sblock2"><div class="title"><b>Resets</b></div></td>
    <td align="center" class="sblock2"><div class="title"><b>Level</b></div></td>
    <td align="center" class="sblock2"><div class="title"><b>Zen</b></div></td>
</tr>
<?php if (count($character_data) > 0) { ?>
<?php foreach ($character_data as $character) { 
    // Get character attributes
    $character_attributes = muonline_check_characters_attribute($character['Id'], $attribute_name_map);
    
    // Replace the keys with attribute names
    $resets_value = $character_attributes['Resets'];
    $level_value = $character_attributes['Level'];
    // Get character Zen
    $zen_value = muonline_get_character_zen($character['InventoryId']);
    
    ?>
<tr>
<td align="center" class="sblock2"><a href="?charname=<?php echo $character['Name']; ?>"><?php echo $character['Name']; ?></a></td>
    <td align="center" class="sblock2"><?php echo $resets_value; ?></td>
    <td align="center" class="sblock2"><?php echo $level_value; ?></td>
    <td align="center" class="sblock2"><?php echo $zen_value; ?></td>
</tr>
<?php } 
} else {
?>
<tr>
    <td align="center" class="sblock2" colspan="5">You have not created any characters yet</td>
</tr>
<?php } ?>
</tbody></table>
<?php
}    
}
    else {
?>
<div class="warning-summary ">You need to login with your gaming account to the site</div>

<?php
    }
    
    return ob_get_clean();
}

add_shortcode('muonline_characters', 'muonline_show_characters');
