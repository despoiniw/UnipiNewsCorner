<?php
/*
Plugin Name: Multiplayer Games
Version: 3.7
Plugin URI: http://www.dimensionex.net/en/wordpress_multiplayer_plugin.htm
Description: Let your users play free games while visiting your WordPress website
Author: Cristiano Leoni
Author URI: http://www.sitiweb-bologna.com/

Quick Start:

1) Activate the Plugin
2) Create a new page and type the shortcode: [games] it will be replaced with the games list
3) Enjoy games

optional:

4) Go to Settings/Multiplayer games and change settings, remove games you dont like, add new ones, edit descriptions, change thumbnails
5) Customize included stylesheet
6) Add your language translation (requires a text editor and a free software named POedit)

Free help and tips can be found on the official web page of the plugin

Versions History:

+ 3.7
	Tweaked for better compatibility with Wordpress 4.4 and default themes, amended games list
+ 3.6
	Added compatibility for Wordpress 3.9
	Bug fixed: AdSense ads did not show up
	Server list and games now up-to date - all servers and games are working
+ 3.5
	Bug fixed: Shortcodes [games] and #games were not working in certain conditions
	Bug fixed: Move up/down in game list was not working properly
	More translations for added (Italian)
+ 3.4	
	Bug fixed: Editing games data won't work in certain circumstances
	Bug fixed: Default game play window size was not working on empty installs
	Documentation fixed
	Javascripts moved to head of page (requires that you have wp_head() in your theme's header.php)
+ 3.3,3.2,3.1
	Fixes to CSS
+ 3.0.0
	Plugin would not work on permalinks structure - fixed
	Added new game: Citadels
	Added multi-language support (sample translation file provided for Italian)
	Added better sylization to page elements 
	New shortcodes: [games] and [game=N], where old #games is kept for backward compatibility
	Configurable Fullscreen play support
	Configurable AdSense support for earning money at zero effort
	Cleaned up the code
+ 2.1.0
	Options moved in the Settings menu, only for administrator access
	Fixed issue: "Play Now" link not showing up in "Whos There" page
	Added stylesheet support (see style.css in plugin folder)
	Added games: Dragon Dice Online, MulliChat
	Removed games: Boundary City (having problems on their website)
+ 2.0.6 -
	Minor fixes
+ 2.0.5 - 2008/10/28
	Added Boundary City game
	Fixed bug that was producing double prints of games when different servers were registered
	Functions renamed for readability
	Tested under WP 2.6.3
+ 2.0 - 2008/05/19
	Tested under Wordpress 2.5.1
	Bugs corrected
	Used filtering method: #gameslist for inserting the games list
+ 1.6
    Used 640x480 as default resolution
+ 19.4.2007  1.4, 1.5
    Re-packaged
+ 18.4.2007  1.3
    Added thumbnails and 1 game to the list (Four-in-Line)
+ 1.2.2006 1.2
    Adaptation to Wordpress 2.1
+ 18.7.2005  1.1
	Allows embedded play (plays game inside your WordPress site - embedded window)
+ 14.7.2005  1.0
	First working version
+ 12.6.2005  0.2
    Admin part is working
+ 30.6.2005  0.1
    First version (based on Downloads 0.2b by Philipp Baer)

Part of this work is based on a plugin by Philipp Baer

Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

$dx_embeddedplay = TRUE;	// Use just embedded playing? (FALSE = plays games in fullscreen window) - use of this setting is not advised
			
$dx_sizes = array(		// Available sizes of the play window (supported by DimensioneX engine
	'640x480'=>'Medium',
	'800x600'=>'Large',
	'480x272'=>'Tiny'
);

$dx_ad_sizes = array(		// Corresponding ad sizes
	'640x480'=>'468x60',
	'800x600'=>'728x90',
	'480x272'=>'250x250'
);


$datefmt = "d.m.Y";//, H:i:s";

define('DX_FORM', 'MultiplayerGames');
define('DX_SERVERS_FORM', 'MultiplayerServers');

// load localisation files
load_plugin_textdomain('multiplayer','wp-content/plugins/multiplayer-plugin/');

/*
 * Create tables required by the plugin
 */
function dx_create_tables() {
    global $wpdb;

    $wpdb->query(
        "CREATE TABLE IF NOT EXISTS $wpdb->dxgames (
         id INTEGER UNSIGNED NOT NULL auto_increment,
         server INTEGER NOT NULL default 0,
         slot VARCHAR(255) NOT NULL default 'noslot',
         name VARCHAR(50) NOT NULL default 'untitled',
         description VARCHAR(255) NOT NULL default '',
         thumb VARCHAR(255) NOT NULL default '',
         category VARCHAR(64) NOT NULL default '',
         owner INTEGER NOT NULL default 1,
         page INTEGER NOT NULL default 0,
         position INTEGER NOT NULL default 0,
         date DATETIME NOT NULL default '0000-00-00 00:00:00',
         creation_date DATETIME NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY(id))");

   // servers table
    $wpdb->query(
        "CREATE TABLE IF NOT EXISTS $wpdb->dxservers (
         id INTEGER UNSIGNED NOT NULL auto_increment,
         name VARCHAR(64) NOT NULL default '',
         url VARCHAR(255) NOT NULL default '',
         PRIMARY KEY(id))");
}


/*
 * Fills game table with (working!) demo games
 */
function dx_fill_tables() {
    global $wpdb;

   // servers table
   $server = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxservers WHERE NAME LIKE 'DimensioneX'");

   if (!$server) {
    $wpdb->query(
        "INSERT INTO $wpdb->dxservers (url,name) VALUES ('http://play.dimensionex.net/dimx/servlet/multiplayer','DimensioneX')");
		$server = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxservers");
	}

	// Display on ANY page using the "Multiplayer Games List" (multiplayerlist.php) template
	$page = -1;

   // games table

	$category = "1 - MMORPG";

	// Add Underworld
    $game = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxgames WHERE NAME='Underworld'");
	if (!$game) {
    	$query =
            "INSERT INTO $wpdb->dxgames(name, description, thumb, server, slot, category, page, position, date, creation_date)" .
            "VALUES(" .
            "'Underworld', " .
            "'Fantasy RPG taking place in a cursed castle. Several character types to choose from.', 'http://www.dimensionex.net/en/images/underworld_logo_small.png'," .
            $server . ", " .
            "5, " .
            "'$category', " .
            $page . ", " .
            dx_game_next_free_position($category) . ", " .
            "'" . current_time('mysql') . "', " .
            "'" . current_time('mysql') . "')";
		$wpdb->query($query);
	}

	// Add Network Arena
    $game = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxgames WHERE NAME='NetWork Arena'");
	if (!$game) {
    	$query =
            "INSERT INTO $wpdb->dxgames(name, description, thumb, server, slot, category, page, position, date, creation_date)" .
            "VALUES(" .
            "'NetWork Arena', " .
            "'NetWork Arena is a virtual world where your goal is to destroy Viruses or other Navigators in the network.', 'http://www.dimensionex.net/en/images/netarena.jpg'," .
            $server . ", " .
            "7, " .
            "'$category', " .
            $page . ", " .
            dx_game_next_free_position($category) . ", " .
            "'" . current_time('mysql') . "', " .
            "'" . current_time('mysql') . "')";
		$wpdb->query($query);
	}

	$category = "2 - Multiplayer Adventure";

	// Add Citadels
    $game = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxgames WHERE NAME='Citadels'");
	if (!$game) {
    	$query =
            "INSERT INTO $wpdb->dxgames(name, description, thumb, server, slot, category, page, position, date, creation_date)" .
            "VALUES(" .
            "'Citadels', " .
            "'Explore the endless maze, get weapons and fight monsters, recharge energy with beer. A unique MMORPG adventure with a singular yet stylish vintage graphics.', 'http://www.dimensionex.net/en/images/citadels_promo_small.jpg'," .
            $server . ", " .
            "10, " .
            "'$category', " .
            $page . ", " .
            dx_game_next_free_position($category) . ", " .
            "'" . current_time('mysql') . "', " .
            "'" . current_time('mysql') . "')";
		$wpdb->query($query);
	}
	
	// Add The Beach
    $game = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxgames WHERE NAME='The Beach'");
	if (!$game) {
    	$query =
            "INSERT INTO $wpdb->dxgames(name, description, thumb, server, slot, category, page, position, date, creation_date)" .
            "VALUES(" .
            "'The Beach', " .
            "'You are on a far distant island, where strange people live. Will you find the hidden treasure before your opponents do?', 'http://www.dimensionex.net/en/images/thebeach.jpg'," .
            $server . ", " .
            "4, " .
            "'$category', " .
            $page . ", " .
            dx_game_next_free_position($category) . ", " .
            "'" . current_time('mysql') . "', " .
            "'" . current_time('mysql') . "')";
		$wpdb->query($query);
	}

	$category = "3 - Board";

	// Add FourInLine
    $game = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxgames WHERE NAME='Four-In-Line'");
	if (!$game) {
    	$query =
            "INSERT INTO $wpdb->dxgames(name, description, thumb, server, slot, category, page, position, date, creation_date)" .
            "VALUES(" .
            "'Four-In-Line', " .
            "'Challenge your opponent: Place four pieces in line. A classic board game.', 'http://www.dimensionex.net/turnbased/images/fourin1_small.jpg'," .
            $server . ", " .
            "17, " .
            "'$category', " .
            $page . ", " .
            dx_game_next_free_position($category) . ", " .
            "'" . current_time('mysql') . "', " .
            "'" . current_time('mysql') . "')";
		$wpdb->query($query);
	}


   // Add Serviteurmine.nu server
   $server = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxservers WHERE NAME LIKE 'Serviteurmine.nu'");

   if (!$server) {
    $wpdb->query(
        "INSERT INTO $wpdb->dxservers (url,name) VALUES ('http://www.dragondiceonline.com/servlet/multiplayer','DragonDice')");
		$server = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxservers");
	}

	$category = "3 - Board";

	// Add Dragon Dice
    $game = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxgames WHERE NAME='Dragon Dice'");
	if (!$game) {
    	$query =
            "INSERT INTO $wpdb->dxgames(name, description, thumb, server, slot, category, page, position, date, creation_date)" .
            "VALUES(" .
            "'Dragon Dice', " .
            "'An RPG board game, please check out the game rules on the <a href=\"http://www.dragondiceonline.com/\">website</a> before playing', 'http://www.dimensionex.net/en/images/dragondice.jpg'," .
            $server . ", " .
            "1, " .
            "'$category', " .
            $page . ", " .
            dx_game_next_free_position($category) . ", " .
            "'" . current_time('mysql') . "', " .
            "'" . current_time('mysql') . "')";
		$wpdb->query($query);
	}

	$category = "4 - Flash games with chat";

   // Add Serviteurmine.nu server
   $server = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxservers WHERE NAME LIKE 'Serviteurmine.nu'");

   if (!$server) {
    $wpdb->query(
        "INSERT INTO $wpdb->dxservers (url,name) VALUES ('http://cotteux.noip.me:8080/ddo/servlet/multiplayer','Serviteurmine.nu')");
		$server = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxservers");
	}
	
	// Add MulliChat
    $game = $wpdb->get_var("SELECT MAX(id) FROM $wpdb->dxgames WHERE NAME='MulliChat'");
	if (!$game) {
    	$query =
            "INSERT INTO $wpdb->dxgames(name, description, thumb, server, slot, category, page, position, date, creation_date)" .
            "VALUES(" .
            "'MulliChat', " .
            "'Violent/Gore Flash Games playarea with chat. WARNING!! - not suitable for children!!', 'http://www.dimensionex.net/en/images/online6.jpg'," .
            $server . ", " .
            "5, " .
            "'$category', " .
            $page . ", " .
            dx_game_next_free_position($category) . ", " .
            "'" . current_time('mysql') . "', " .
            "'" . current_time('mysql') . "')";
		$wpdb->query($query);
	}

}


/*
 * Drops game tables
 */
function dx_drop_tables() {
    global $wpdb;

	$row = @$wpdb->get_row("SELECT DISTINCT(thumb) FROM $wpdb->dxgames");

	if (!$row) { // Transition from old version - remove table
		$wpdb->query("DROP TABLE $wpdb->dxgames");
		$wpdb->query("DROP TABLE $wpdb->dxservers");
		echo "Please ignore this error - database was adjusted - please hit REFRESH<p><br><p>";
	}
}


/*
 * Loads url-localdir-mappings from the database
 */
function dx_load_mappings() {
    global $wpdb;
    global $mappings;

    $results = $wpdb->get_results(
        "SELECT * FROM $wpdb->dxservers");

    if ($results) {
        foreach ($results as $result) {
            $mappings[$result->url] = $result->localpath;
        }
    }
}


/*
 * Initialize Downloads
 * - Setup database
 * - Load url-localdir-mappings
 */
function dx_initialize() {
    global $table_prefix;
    global $wpdb;

    // Register database names
    $wpdb->dxgames = $table_prefix . 'dxgames';
    $wpdb->dxservers = $table_prefix . 'dxservers';

    // Create tables in database if required
    dx_create_tables();

    // Load mappings from database
    dx_load_mappings();

}


/*
 * Returns the value of $name if it is an integer
 * $default otherwise.
 */
function dx_get_int($name, $default = NULL) {
    $result = $default;

    // Get the value if it is an integer
    if (isset($_REQUEST[$name])) {
        if (is_numeric($_REQUEST[$name])) {
            $result = $_REQUEST[$name];
        }
    }

    return $result;
}


/*
 * Echoes out a list (option fields for select) of categories for the passed page id.
 * If the page id is -1, all categories are returned
 */
function dx_get_category_list($category = NULL) {
    global $wpdb;

    $results = $wpdb->get_results(
        "SELECT DISTINCT category " .
        "FROM $wpdb->dxgames " .
        "ORDER BY category");

    foreach ($results as $row) {
?>
          <option value="<?php echo $row->category; ?>"<?php echo ($category == $row->category ? " selected=\"selected\"" : ""); ?>><?php echo wp_specialchars($row->category); ?></option>
<?php
    }
}


/*
 * Echoes out a list (option fields for select) of categories for the passed page id.
 * If the page id is -1, all categories are returned
 */
function dx_get_server_list($server) {
    global $wpdb;

    $results = $wpdb->get_results(
        "SELECT * " .
        "FROM $wpdb->dxservers " .
        "ORDER BY name");

    foreach ($results as $row) {
?>
          <option value="<?php echo $row->id; ?>"<?php echo ($server == $row->id ? " selected=\"selected\"" : ""); ?>><?php echo wp_specialchars($row->name); ?></option>
<?php
    }
}


/*
 * Remove a server
 */
function dx_mapping_remove($id) {
    global $wpdb;
    $wpdb->query(
        "DELETE FROM $wpdb->dxservers WHERE id=$id");
}


/*
 * Remove a game
 */
function dx_remove($id) {
    global $wpdb;

	// Remove file
    $results = $wpdb->get_results(
        "SELECT * " .
        "FROM $wpdb->dxgames " .
        "WHERE id=$id");

	if ($results) {
		foreach ($results as $row) {
			$wpdb->query(
				"DELETE FROM $wpdb->dxgames WHERE id=$id");
		}
	} else {
	   echo ("Unexistent> $id");
	}
	return;


}


/*
 * Get the next free position withing a category in a page
 */
function dx_game_next_free_position($category) {
    global $wpdb;

    $result = $wpdb->get_var(
        "SELECT MAX(position) " .
        "FROM $wpdb->dxgames ".
        "WHERE category='$category'");

    return (isset($result) ? $result + 1 : 0);
}


/*
 * Move a game entry within a category in a page one position up
 */
function dx_game_move_up($id) {
    global $wpdb;

    $row = $wpdb->get_row(
        "SELECT id, page, category, position " .
        "FROM $wpdb->dxgames WHERE id=$id");

    // Check if there is such a row
    if ($row) {
        $position = $row->position;

        // Move element up iff position is greater than 0
        if ($position > 0) {
            $position--;

            $prevrow = $wpdb->get_row(
                "SELECT id, position " .
                "FROM $wpdb->dxgames " .
                "WHERE category='$row->category' AND position<=$position " .
                "ORDER BY position DESC");

            // If there is a previous element, move it one position down
            if ($prevrow) {
                $position = $prevrow->position;
                $prevrow_position = $position + 1;
                $wpdb->query(
                    "UPDATE $wpdb->dxgames " .
                    "SET position=$prevrow_position WHERE id=$prevrow->id");
            }

            // Move the current element one to the new position
            $wpdb->query(
                "UPDATE $wpdb->dxgames " .
                "SET position=$position " .
                "WHERE id=$id");
        }
    }
}


/*
 * Move a game entry within a category in a page one position down
 */
function dx_game_move_down($id) {
    global $wpdb;

    $row = $wpdb->get_row(
        "SELECT id, position, page, category " .
        "FROM $wpdb->dxgames WHERE id=$id");

    // Check if there if such a row
    if ($row) {
        $position = $row->position;

        // Get the last game id
        $next_free = dx_game_next_free_position($row->category) - 1;

        // Move element down iff we're not at the end of the list
        if ($next_free > $row->position)  {
            $position++;

            $nextrow = $wpdb->get_row(
                "SELECT id, position " .
                "FROM $wpdb->dxgames " .
                "WHERE category='$row->category' AND position>=$position " .
                "ORDER BY position ASC");

            // If there is a previous element, move it one position up
            if ($nextrow) {
                $position = $nextrow->position;
                $nextrow_position = $position - 1;
                $wpdb->query(
                    "UPDATE $wpdb->dxgames " .
                    "SET position=$nextrow_position " .
                    "WHERE id=$nextrow->id");
            }

            // Move the current element one to the new position
            $wpdb->query(
                "UPDATE $wpdb->dxgames " .
                "SET position=$position " .
                "WHERE id=$id");
        }
    }
}


/*
 * Create or commit changes for new/existent mapping
 */
function dx_servers_commit_changes($id, $url, $localpath) {
    global $wpdb;

    if ($id > -1) {
        // If an id is given, try to update the entry
        $query =
            "UPDATE $wpdb->dxservers " .
            "SET url='$url', name='$localpath' " .
            "WHERE id=$id";

    } else {
        // Otherwise insert a new one
        $query =
            "INSERT INTO $wpdb->dxservers(url, name) " .
            "VALUES(" .
            "'$url', " .
            "'$localpath')";
    }

    // Fire query :)
    $wpdb->query($query);
}


/*
 * Create or commit changes for new/existent game
 */
function dx_commit_changes($id, $name, $description, $thumb, $server, $slot, $category) {
    global $wpdb;

    if ($id > -1) {
        // If an id is given, try to update the entry
        $query =
            "UPDATE $wpdb->dxgames " .
            "SET name='$name', description='$description', thumb='$thumb', server='$server', slot='$slot', category='$category', page=$page, date='" . current_time('mysql') . "' " .
            "WHERE id=$id";

    } else {
        // Otherwise insert a new entry after the last one
        $query =
            "INSERT INTO $wpdb->dxgames(name, description, thumb, server, slot, category, page, position, date, creation_date)" .
            "VALUES(" .
            "'$name', " .
            "'$description', " .
          	"'$thumb', " .
            "'$server', " .
            "'$slot', " .
            "'$category', -1, " .
            dx_game_next_free_position($category) . ", " .
            "'" . current_time('mysql') . "', " .
            "'" . current_time('mysql') . "')";
    }

    // Fire query :)
    $wpdb->query($query);
}


/*
 * Form for adding a server
 */
function dx_server_edit_form($id = 0, $url = '', $localpath = '') {
    global $wpdb;

?>
  <div class="wrap">
<?php
	echo dx_pluginsettings_title(__('Edit Server','multiplayer'),null);
?>  
    <form name="mapping_edit" method="post" action="<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_SERVERS_FORM; ?>&dx_action=edit">
<?php

    // If an id is given, an existing entry is modified. Silently pass the
    // id as a hidden parameter
    if ($id > 0) {
?>
      <input type="hidden" name="dx_id" value="<?php echo $id; ?>" />
<?php
    }

?>
      <input type="hidden" name="dx_action" value="mapping_commit" />
      <table style="border: 0;">
        <tr>
          <td>Name:</td>
          <td><input type="text" SIZE="50" name="dx_mapping_localpath" value="<?php echo $localpath; ?>" /></td>
        </tr>
        <tr>
          <td>URL:</td>
          <td><input type="text" SIZE="80" name="dx_mapping_url" value="<?php echo $url; ?>" /></td>
        </tr>
      </table><br />
      <input type="submit" name="submit" value="<?= _e('Save'); ?>">
    </form>
  </div>
<?php
}


/*
 * Form for editing/creating a game entry
 */
function dx_edit_form($id = 0, $name = 'untitled', $description = '', $thumb = '', $server = "", $slot = '', $category = 0, $page_id = -1) {
    global $wpdb;
    global $dx_updmsg;

?>
  <div class="wrap">
<?php
	dx_pluginsettings_title(__('Edit Game','multiplayer'));
	
    // If settings updated
    if ($dx_updmsg) {
?>
	<div style="font-weight: bold; font-size:2em; color: #00a; text-align: center;"><?= $dx_updmsg; ?></div><br/>
<?php
    }
	echo '<form name="edit" method="post" action="'. $_SERVER[PHP_SELF].'?page='.DX_FORM.'">'."\n";

    // If an id is given, an existing entry is modified. Silently pass the
    // id as a hidden parameter
    if ($id > 0) {
?>
      <input type="hidden" name="dx_id" value="<?php echo $id; ?>" />
<?php
    }

?>
      <input type="hidden" name="dx_action" value="commit" />
      <table style="border: 0;">
        <tr>
          <td>Server:</td>
          <td width="40%">
            <select name="dx_server_id">
			<?php dx_get_server_list($server); ?>
            </select>
          </td>
          <td>Choose from the list. If the list is empty, then add a server with the Servers tab.</td>
        </tr>
        <tr>
          <td>Slot:</td>
          <td><input type="text" name="dx_slot" SIZE="8" value="<?php echo $slot; ?>" /></td>
          <td>Write here the slot associated to the game. This is usually a number like 1,2,3...</td>
        </tr>
        <tr>
          <td>Name:</td>
		  <td><input type="text" name="dx_name" SIZE="20" value="<?php echo $name; ?>" /></td>
          <td><?php _e('Name of the game','multiplayer'); ?></td>
        </tr>
        <tr>
          <td>Description:</td>
          <td><TEXTAREA NAME="dx_description" ROWS="4" COLS="30"><?php echo($description); ?></TEXTAREA>
          </td>
          <td>Brief description of the game. May contain up to 255 characters.</td>
        </tr>
        <tr>
          <td>Thumbnail URL:</td>
          <td><TEXTAREA NAME="dx_thumb" ROWS="4" COLS="30"><?php echo($thumb); ?></TEXTAREA>
          </td>
          <td>URL to game's thumbnail. May contain up to 255 characters.</td>
        </tr>
        <tr>
          <td>Group:</td>
          <td>
            <select name="dx_category">
<?php

    // Print out all categories
    dx_get_category_list($category);

?>
            </select><BR>...or new:
            <input type="text" name="dx_category_new" value="" />
          </td>
          <td>Use this one to group entries together (by genre, language, etc.). If unsure, just leave blank.</td>
        </tr>
      </table><br />
      <input type="submit" name="submit" value="<?= _e('Save'); ?>">
    </form>
  </div>
<?php
}


/*
 * Register the plugin with the management console
 * - Register management page
 * - Call dx_initialize
 */
function dx_register() {
    global $table_prefix;
    global $wpdb;
    global $dx_show_category;
    global $dx_edit;
    global $dx_mapping;
    global $dx_mapping_edit;
    global $dx_mapping_commit;
    global $dx_error;
    global $dx_commited;
    global $dx_db;
    global $dx_map_db;
    global $dx_trigger_action;
    global $dx_updmsg;

    if (function_exists('add_options_page')) {
        add_options_page('MultiplayerGames', __('Multiplayer Games','multiplayer'), 'administrator', DX_FORM, 'dx_display_management_panel');
        add_options_page('MultiplayerServers', __('Multiplayer Servers','multiplayer'), 'administrator', DX_SERVERS_FORM, 'dx_display_management_panel_mappings');
    }

	if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
		dx_fill_tables();
	}

	if (isset($_GET['action']) && $_GET['action'] == 'deactivate') {
		//dx_drop_tables();
	}

    // Initialize plugin
    dx_initialize();

    // Check for the category that should be displayed
    $dx_show_category = '';
    if (isset($_POST['dx_show_category'])) {
        // Use parameter value
        $dx_show_category = $_POST['dx_show_category'];
    } else if (isset($_COOKIE['dx_show_category_' . COOKIEHASH])) {
        // Use cookie information
        $dx_show_category = $_COOKIE['dx_show_category_' . COOKIEHASH];
    }

    // Store new cookies
    setcookie('dx_show_category_' . COOKIEHASH, $dx_show_category, time() + 600);

    // Initialize the global variables that indicate which input form
    // should be displayed.
    $dx_trigger_action = '';
    $dx_edit = -1;

    // Check the action
    if (isset($_REQUEST['dx_action'])) {
        switch ($_REQUEST['dx_action']) {
            case 'mapping_new':
            case 'new':
                // Trigger creation of a new game or gameserver mapping
                $dx_trigger_action = $_REQUEST['dx_action'];
                break;

            case 'mapping_commit':
                $commit = -1;
                $dx_commited = false;

                // Get the item id, if this is an updatea
                if (isset($_REQUEST['dx_id'])) {
                    $commit = dx_get_int('dx_id');
                    if ($commit == NULL) {
                        // The parameter value is not an integer... abort
                        break;
                    }
                }

                // At least these two parameters must be present
                if ((isset($_REQUEST['dx_mapping_url'])) &&
                    (isset($_REQUEST['dx_mapping_localpath'])))
                {
                    // Sanitize strings
                    $url = wp_specialchars(trim($_REQUEST['dx_mapping_url']));
                    $localpath = wp_specialchars($_REQUEST['dx_mapping_localpath']);

                    // Check if the URL is valid (for our purposes)
                    $urltest = parse_url($url);
                    if ((strlen($urltest['scheme']) > 0) && (strlen($urltest['host']) > 0)) {
                        // Commit changes
                        dx_servers_commit_changes(
                            $commit,
                            $url,
                            $localpath);

                        // Indicate successful commitment
                        $dx_commited = true;
                        
                        $dx_updmsg = __('Settings updated','multiplayer');

                    } else {
                        // An error occured, nothing's be commited
                        $dx_error = __('The specified URL is not valid','multiplayer');
                    }
                } else {
                    $dx_error = "Please specify URL and Server Name!";
                }
                break;

            case 'commit':
                $commit = -1;
                $dx_commited = false;

                // Get the item id, if this is an update
                $commit = dx_get_int('dx_id');
                if ($commit == NULL) {
                    $commit = -1;
                }

                // At least these three parameters must be present
                if ($_REQUEST['dx_slot'])
                {
                	// Get the server ID and slot ID
                	$server = $_REQUEST['dx_server_id'];
                	$slot = $_REQUEST['dx_slot'];

                    // Get the category to which the item should be added
                    if ((isset($_REQUEST['dx_category_new'])) && ($_REQUEST['dx_category_new'] != "")) {
                        // If dx_category_new is available, a new category is created
                        $category = $_REQUEST['dx_category_new'];

                    } else {
                        // Otherwise the dx_category variable must be present
                        if (!isset($_REQUEST['dx_category']))
                            $category = "Miscellanous";
                        else
                        	$category = $_REQUEST['dx_category'];
                    }

                    // Sanitize parameters
                    $description = $_REQUEST['dx_description'];
                    $description = wp_specialchars($description);
                    $name = $_REQUEST['dx_name'];
                    $name = wp_specialchars($name);
                    $thumb = $_REQUEST['dx_thumb'];
                    $thumb = wp_specialchars($thumb);
                    $category = wp_specialchars($category);

                    // Commit changes
                    dx_commit_changes(
                        $commit,
                        $name,
                        $description,
                        $thumb,
                        $server,
                        $slot,
                        $category);

                    // Indicate successful commitment
                    $dx_commited = true;
                    
                    $dx_updmsg = __('Settings updated',multiplayer);

                } else {
                    // An error occured, nothing's be commited
                    $dx_error = "Please specify a page on which this game should be displayed!";
                }
                break;

            case 'mapping_delete':
                $id = dx_get_int('dx_id');
                if ($id != NULL) {
                    // Remove the specified mapping
                    dx_mapping_remove($id);
                }
                break;

            case 'delete':
                $id = dx_get_int('dx_id');
                if ($id != NULL) {
                    // Remove the specified game
                    dx_remove($_REQUEST['dx_id']);
                }
                break;

            case 'mapping_edit':
                // Get the id for the mapping to edit
                $dx_mapping_edit = dx_get_int('dx_id');
                break;

            case 'edit':
                // Get the id for the game to edit
                $dx_edit = dx_get_int('dx_id');
                break;

            case 'move_up':
                // Move the game one position up
                $id = dx_get_int('dx_id');
                if ($id != NULL) {
                    dx_game_move_up($id);
                }
                break;

            case 'move_down':
                // Move the game one position down
                $id = dx_get_int('dx_id');
                if ($id != NULL) {
                    dx_game_move_down($id);
                }
                break;
        }
    }
}

/*
 * Form for options (not implemented yet)
 */
function dx_display_options_panel() {
    if (isset($_POST['info_update'])) {
?>
  <div class="updated"><p><strong>
    <?php _e('Process completed fields in this if-block, and then print warnings, errors or success information.', 'Localization name') ?>
  </strong></p></div>

<?php
    }

?>
  <div class=wrap>
    <form method="post">
      <h2>WP Downloads</h2>
      <fieldset name="set1">
        <legend><?php _e('Options set 1', 'Localization name') ?></legend>
        Put some form input areas here.
      </fieldset>
      <fieldset name="set2">
        <legend><?php _e('Options set 2', 'Localization name') ?></legend>
        Put some more form input areas here.
      </fieldset>
      <div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Update options', 'Localization name') ?> »" >
      </div>
    </form>
  </div>
<?php
}


/*
 * Form for managing games
 */
function dx_display_management_panel() {
    global $wpdb;
    global $dx_show_category;
    global $dx_trigger_action;
    global $dx_edit;
    global $dx_error;
    global $dx_updmsg;
    
    $dx_playsize = dx_get_playsize();
    $dx_adsensepubid = dx_get_adsensepubid();
    $dx_enablefullscreen = dx_get_enablefullscreen();
    $dx_enableadsense = dx_get_enableadsense();
    
    if (isset($_POST['playsize'])) {
        // Use parameter value
        $dx_playsize = $_POST['playsize'];
        update_option('dx_play_size', $dx_playsize);
        $dx_updmsg = __('Settings updated',multiplayer);
    }


    if (isset($_POST['adsensepubid'])) {
        // Use parameter value
        $dx_adsensepubid = trim($_POST['adsensepubid']);
        update_option('dx_adsensepubid', $dx_adsensepubid);
        $dx_updmsg = __('Settings updated',multiplayer);
    }
  
    if (isset($_POST['action'])) { // Checkboxes
        // Use parameter value
        $dx_enablefullscreen = $_POST['enablefullscreen'];
        update_option('dx_enablefullscreen', $dx_enablefullscreen);

        $dx_enableadsense = $_POST['enableadsense'];
        update_option('dx_enableadsense', $dx_enableadsense);

        $dx_updmsg = __('Settings updated',multiplayer);
    }

    $dx_edit=dx_get_int('dx_id');
    
    if ($dx_edit > -1 && $_POST['dx_action']=='commit') {
                $sql=
                    "UPDATE $wpdb->dxgames " .
                    "SET name='${_POST[dx_name]}',
                    description='${_POST[dx_description]}',
                    thumb='${_POST[dx_thumb]}',
                    server='${_POST[dx_server_id]}',
                    slot='${_POST[dx_slot]}',
                    category='".($_POST['dx_category_new']?$_POST['dx_category_new']:$_POST['dx_category'])."'
                    WHERE id='$dx_edit'";
                    //echo $sql;
                    
                $wpdb->query($sql);
    }

    $skip_panel=FALSE;
    
    // If an action was triggered, display the corresponding form
    if ($dx_trigger_action == 'new') {
        dx_edit_form();
        $skip_panel=TRUE;

    } else if ( ($dx_edit > -1) && (!in_array($_REQUEST['dx_action'],array('move_up','move_down') ) ) ) {
    
	$wpdb->show_errors();
	$row = $wpdb->get_row(
            "SELECT g.* " .
            "FROM $wpdb->dxgames as g " .
            "WHERE g.id='$dx_edit'");
            
        dx_edit_form(
            $dx_edit,
            $row->name,
            $row->description,
            $row->thumb,
            $row->server,
            $row->slot,
            $row->category);
            
        $skip_panel=TRUE;
    }

    
if (!$skip_panel) :
?>
  <div class="wrap">
<?php
	echo dx_pluginsettings_title(__('Main Configuration','multiplayer'),null);
?>
	<form name="settings" method="post" action="">
      <table cellpadding="5" cellspacing="0">
      <tr><td><?= _e('Play window size','multiplayer'); ?></td><td><?php dx_getsizesselect($dx_playsize); ?></td><td><i>Choose the best size according to your Wordpress theme</i></td></tr>
      <tr><td><?= _e('Enable [Play Fullscreen] button','multiplayer'); ?></td><td><input type="checkbox" name="enablefullscreen" value="1" <?= ($dx_enablefullscreen)?'checked="checked" ':''?> /></td><td><i>Check this if you want to give players the capability to swith full-screen for best playing experience</i></td></tr>
      <tr><td><?= _e('Add AdSense ads to games','multiplayer'); ?></td><td><input type="checkbox" name="enableadsense" value="1" <?= ($dx_enableadsense)?'checked="checked" ':''?> /></td><td><i>Check this if you would like to <b>earn money</b> with Google AdSense at zero effort: Ads will appear near games. If this is checked you should enter you publisher ID below.</i></td></tr>
      <tr><td><?= _e('AdSense publisher ID to be used in ads','multiplayer'); ?></td><td><input type="text" name="adsensepubid" value="<?= $dx_adsensepubid ?>" /></td><td><i>Paste here your own AdSense publisher ID ("pub-#######...") - If you don't have one, <a target="_blank" href="https://www.google.com/adsense/">apply here</a> or leave blank for donating to the plugin's developer.</i></td></tr>
    </table>
<input type="submit" name="action" value="<?= _e('Save'); ?>" />    
  </form><br/>
</div>
<?php


    // If settings updated
    if ($dx_updmsg) {
?>
    <div class="wrap">
	<div style="font-weight: bold; font-size:2em; color: #00a; text-align: center;"><?= $dx_updmsg; ?></div>
    </div>
<?php
    }

    // If an error occured, print out the message
    if ($dx_error != '') {
?>
    <div class="wrap">
	<div style="font-weight: bold; color: #f00; text-align: center;"><?php echo $dx_error; ?></div>
    </div>
<?php
    }



?>
<form name="links" id="links" method="post" action="">
  <div class="wrap">
    <input type="hidden" name="dx_link_id" value="" />
    <input type="hidden" name="dx_action" value="" />
    <input type="hidden" name="dx_page_id" value="-1" />
    <input type="hidden" name="dx_show_category" value="" />
<?php

    $filter = "WHERE g.server = s.id ";

    // If a specific category was chosen, define a filter
    if ($dx_show_category != "") {
        $filter .= " AND category='" . $dx_show_category . "' ";
    }

    $results = $wpdb->get_results(
        "SELECT g.*, s.url " .
        "FROM $wpdb->dxgames AS g, $wpdb->dxservers AS s " .
        $filter .
        "ORDER BY g.category, g.position");

	echo '<h2>'.__('Games List','multiplayer')."</h2>\n";
	echo '<p>'.__('Tip: insert the shortcode [games] in any page or post to display games list','multiplayer')."</p>\n";
?>
      <table style="border: 0; width: 100%;">
<?php

    if ($results) {
        // Go through the results an list all games for each category
        $styles = array();
        $styles[] = '';
        $styles[] = 'alternate';

        $old_category = '';
        $i = 0;
        foreach ($results as $row) {
            // Once the category name changed, insert a new header
            if ($old_category != $row->category) {
            
            
?>
        <tr>
          <td colspan="4">
            <br />
            <h3><?php echo $row->category; ?></h3>
          </td>
        </tr>
<?php
            }

            // Track the category name
            $old_category = $row->category;
?>
        <tr class="<?php echo $styles[$i]; ?>">
          <td>
          <a href="<?php echo $row->url . "?game=" . $row->slot; ?>" title="<?php echo $row->name; ?>"><?php echo($row->name); ?></a>
          </td>
          <td style="width: 60%">
            <?php echo $row->description; ?>
          </td>
          <td>
          Shortcode: [game=<?= $row->id?>]
          </td>
          <td style="text-align: right; width: 20%">
            <a href="<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_FORM; ?>&dx_id=<?php echo $row->id; ?>&dx_action=edit"><?php _e('Edit'); ?></a>
            <a href="<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_FORM; ?>&dx_id=<?php echo $row->id; ?>&dx_action=delete" onclick="return confirm('You are about to delete this link.\n  \'Cancel\' to stop, \'OK\' to delete.');"><?php _e('Delete'); ?></a>
            <a href="<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_FORM; ?>&dx_id=<?php echo $row->id; ?>&dx_action=move_up"><?php _e('Up','multiplayer'); ?></a>
            <a href="<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_FORM; ?>&dx_id=<?php echo $row->id; ?>&dx_action=move_down"><?php _e('Down','multiplayer'); ?></a>
          </td>
        </tr>
<?php
            $i++;
            $i %= 2;
        }
    }

?>
    </table>
  </div>
</form>
<br/>
<p>
    <input type="button" onClick="javascript:document.location.href='<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_FORM; ?>&dx_action=new';" value="<?= __('New Game','multiplayer')?>" />
     <input type="button" onClick="javascript:document.location.href='<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_SERVERS_FORM; ?>';" value="<?= __('Game Servers','multiplayer')?>" />
<?php
	endif; /* skip_panel = FALSE */
	
	if ($skip_panel) {

		echo '<br/><br><input type="button" onClick="javascript:document.location.href=\''.$_SERVER[PHP_SELF].'?page='. DX_FORM. '\';" value="'.__('Games List','multiplayer').'" />';
	}
?>
</p>
<?php
}


/*
 * Form for managing mappings
 */
function dx_display_management_panel_mappings() {
    global $wpdb;
    global $dx_trigger_action;
    global $dx_mapping_edit;
    global $dx_mapping_commit;
    global $dx_commited;
    global $dx_error;

    // If an error occured, print out the error message
    if ($dx_error != '') {
    	echo '<div class="wrap">';
    	dx_pluginsettings_title('Mappings');
	echo '
        <div style="font-weight: bold; color: #f00; text-align: center;">'. $dx_error .'</div>
    </div>';

        // If data was not commited, display the edit form again
        if (!$dx_commited) {
            dx_server_edit_form(
                $dx_mapping_commit,
                $_REQUEST['dx_mapping_url'],
                $_REQUEST['dx_mapping_localpath']);
        }
    }
    // If an action was triggered, display the corresponding form
    if ($dx_trigger_action == 'mapping_new') {
        dx_server_edit_form();

    } else if ($dx_mapping_edit > -1) {
        $row = $wpdb->get_row(
            "SELECT * " .
            "FROM $wpdb->dxservers " .
            "WHERE id=$dx_mapping_edit");

        dx_server_edit_form(
            $dx_mapping_edit,
            $row->url,
            $row->name);
    }

?>
  <div class="wrap">
<?php
	echo dx_pluginsettings_title(__('Game Servers','multiplayer'),null);
?>
    A <i>DimensioneX Server</i> is a web server equipped with the <a href="http://www.dimensionex.net" target="_blank">DimensioneX game engine</a>, offering free multiplayer games to public.<br />
    For each server, you need to specify the URL to its <b>multiplayer</b> servlet. Game slot and other parameters will be added automatically by this plugin.
  </div>
<form name="downloadmappings" id="links" method="post" action="">
  <div class="wrap">
    <input type="hidden" name="dx_mappings_action" value="" />
      <table style="border: 0; width: 100%;" width="100%">
        <tr>
          <th style="text-align: left;">Name</th>
          <th style="text-align: left;">URL</th>
          <th></th>
        </tr>
<?php

    // Get the mappings
    $results = $wpdb->get_results(
        "SELECT * " .
        "FROM $wpdb->dxservers " .
        "ORDER BY id");

    // Print each mapping
    if ($results) {
        $styles = array();
        $styles[] = '';
        $styles[] = 'alternate';

        $i = 0;
        foreach ($results as $row) {
?>
        <tr class="<?php echo $styles[$i]; ?>">
          <td style="width: 35%;">
            <?php echo $row->name; ?>
          </td>
          <td style="width: 35%;">
            <?php echo $row->url; ?>
          </td>
          <td style="width: 30%; text-align: right;">
            <a href="<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_SERVERS_FORM; ?>&dx_id=<?php echo $row->id; ?>&dx_action=mapping_edit"><?php _e('Edit'); ?></a>
            <a href="<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_SERVERS_FORM; ?>&dx_id=<?php echo $row->id; ?>&dx_action=mapping_delete" onclick="return confirm('You are about to delete this link.\n  \'Cancel\' to stop, \'OK\' to delete.');"><?php _e('Delete'); ?></a>
          </td>
        </tr>
<?php
            $i++;
            $i %= 2;
        }
    }

?>
    </table>
    <br />
    <h3><a href="<?php echo $_SERVER[PHP_SELF]; ?>?page=<?php echo DX_SERVERS_FORM; ?>&dx_action=mapping_new"><?php _e('Add Game Server &raquo;','multiplayer'); ?></a></h3>
  </div>
</form>
<?php
}


/*
 * Template function:
 * Get all games. The result is filtered depending on the page and the category.
 */
function dx_get_games($category = "") {
    global $wpdb;
    global $dx_db;
    global $datefmt;
    global $dx_loop_entries;
    global $dx_loop_category;
    global $dx_embeddedplay;
    global $dx_ad_sizes;

	$adsense = dx_get_enableadsense();
	$pubid = dx_get_adsensepubid();
	$playsize = dx_get_playsize();

    dx_loop_load($category);

    $result = "<table id=\"games\">\n";
    while (dx_loop_next()) {
        $result .= dx_loop_get_category($pre.'<tr><td class="dx_category" colspan="3"><h3>', "</h3></tr>\n");
        
        $fixedurl = dx_loop_get_url();
        
    	$dbo = current($dx_loop_entries);
    	$name = $dbo->name;
    	$thumb = $dbo->thumb;
    	$descr = $dbo->description;
    	$gameid=$dbo->id;
    	$slot=$dbo->slot;
    	$serverurl=$dbo->url;
        
        $result .= "<tr valign=\"top\"><td class=\"whosthere\">" . dx_loop_get_players($serverurl,$slot) . "<br/>" . dx_loop_get_whosonlink($gameid,__("Who's there",'multiplayer')) . "</td>\n";
        
        if ($dx_embeddedplay) {
	        $link = dx_generate_game_url($gameid,__('Play','multiplayer')." $name!"); // Might be an A tag or a javascript building it
	} else {
		$link = dx_generate_game_real_url($fixedurl);
	}
        
        $result .= "<td class=\"gameicon\">$link<img src=\"$thumb\" /></a></td>\n";
        $result .= "<td><h4 class=\"dx_name\">$name</a></h4>\n";
        $result .= "<div class=\"dx_description\">$descr</div>\n";
        $result .= "</td></tr>\n";
    }

	if ($adsense && ($dx_loop_category!='')) {
		$result .= '<tr><td colspan="3">'.dx_adsense($dx_ad_sizes[$playsize],$pubid);
		$result .= '</td></tr>';
	}
	
	$result .= "</table>";

    return $result;
}


/*
 * Reset the output loop
 */
function dx_loop_reset() {
    global $dx_loop_entries_start;
    global $dx_loop_entries;
    global $dx_loop_first;
    global $dx_loop_category;

    $dx_loop_entries = $dx_loop_entries_start;
    $dx_loop_first = true;
    $dx_loop_category = '';
}


/*
 * Initialize the output loop
 */
function dx_loop_load($category = '') {
    global $wpdb;
    global $dx_loop_entries_start;
    global $dx_loop_entries;
    global $dx_loop_first;
    global $dx_loop_category;

    $dx_loop_category = '';
    $dx_loop_first = true;

    $filter = "WHERE g.server = s.id ";

    // If a category name is given, modify the filter as required
    if ($category != "") {
        $filter .= "AND category='$category' ";
    }


    $dx_loop_entries = $wpdb->get_results(
        "SELECT g.*,s.url " .
        "FROM $wpdb->dxgames as g, $wpdb->dxservers as s " .
        $filter .
        "ORDER BY category, position");

    $dx_loop_entries_start = $dx_loop_entries;
}


/*
 * Continue with the next element in the output loop
 */
function dx_loop_next() {
    global $dx_loop_entries;
    global $dx_loop_first;

    if ($dx_loop_first) {
        $dx_loop_first = false;
        if ($dx_loop_entries) {
            return (current($dx_loop_entries) != false);
        } else {
            return false;
        }
    }

    return (next($dx_loop_entries) != false);
}


/*
 * Returns the current category. If this was already done an empty string
 * is retunred instead
 */
function dx_loop_get_category($before = '', $after = '') {
    global $dx_loop_entries;
    global $dx_loop_category;

    $dbo = current($dx_loop_entries);

    if ($dbo) {
	$category = $dbo->category;
        if ($category != $dx_loop_category) {
            $dx_loop_category = $category;
            return $before . ($category) . $after;
        }
    }

    return '';
}


/*
 * Returns the current game's LINK-making tag
 */
function dx_generate_game_url($gameid,$title=NULL) {
	return '<a '.($title?"title=\"$title\"":'').' href="'.dx_getembeddedplayurl($gameid).'">';
}

/*
 * Returns the current game's LINK-making tag
 */
function dx_generate_game_real_url($url) {
	return '<script type="text/javascript">genurl("'.$url.'");</script>';
}


/*
 * Returns the current game's real (physical) url
 */
function dx_loop_get_url() {
    global $dx_loop_entries;

    $dbo = current($dx_loop_entries);

	return ($dbo->url) . "?game=" . ($dbo->slot);
}


/*
 * Returns the player's IFRAME
 */
function dx_loop_get_players($serverurl,$slot) {

	return "\n<iframe width=\"100\" height=\"26\" frameborder=\"0\" scrollbars=\"0\" scrolling=\"off\" src=\"" . $serverurl . "?game=" . $slot . "&amp;view=players\"></iframe>\n";
}



/*
 * Returns the current date
 */
function dx_loop_get_date($before = '', $after = '', $fmt = 'd.m.Y') {
    global $dx_loop_entries;

    $dbo = current($dx_loop_entries);

    return $before . (date($fmt, strtotime($dbo->date))) . $after;
}

function dx_contentfilter($content = '') {
	$view = $_REQUEST['view'];

	$games_content = '';
	if (!(stripos($content,'[games]')===FALSE) || !(stripos($content,'#games')===FALSE)) {

		switch ($view) {
			case 'whosthere':
				$games_content .= dx_thewhostherepage($_REQUEST["game"]);
				break;
			case 'play':
				$games_content .= dx_theembeddedplaypage($_REQUEST["game"]);
				break;
			default:
				$games_content .= dx_get_games();
		}
		$content = str_ireplace('[games]',$games_content,$content);
		$content = str_ireplace('#games',$games_content,$content);
		
	} else if (preg_match('/\[game=([0-9])+\]/im',$content,$matches)) {
		//$content .= 'Shortcode found!' . print_r($matches,true);
		$gameid=1;
		switch ($view) {
			case 'whosthere':
				$games_content .= dx_thewhostherepage($gameid);
				break;
			case 'play':
			default:
				$games_content .= dx_theembeddedplaypage($gameid);
		}
		$content = str_ireplace("[game=$gameid]",$games_content,$content);
	}


	
	return $content;
}

function dx_thescripts() {

list($dx_embedded_width,$dx_embedded_height)=dx_getsizes();

return '<script type="text/javascript">
function launch(url) {
	// Startup code for the client
	var w = screen.width - 10;
	var h = screen.height - 100;
	var attrs = "resizable=1,status=1,scrollbars=1,directories=0,toolbar=1,menubar=0,left=0,top=0,width="+w+",height="+h;
	//alert("\'" + attrs + "\'");
	//game = window.open(url + "?width="+screen.width+"&height="+screen.height,\'game\',"\'" + attrs + "\'");
	this.document.location.href = url + "&width="+screen.width+"&height="+screen.height;
}
function genurl(url) {
	// Startup code for the client
	document.write (\'<a href="\' + url + "&width="+screen.width+"&height="+screen.height + \'">\');
}

</script>
<link rel="stylesheet" href="'.get_bloginfo('wpurl') . '/wp-content/plugins/multiplayer-plugin/style.css" type="text/css" media="screen" />
';
}

/*
 * Returns the "Who's there" link
 */
function dx_loop_get_whosonlink($gameid,$prompt) {

	$url=dx_selfURL();
	$url=dx_remove_querystring_var($url, 'view');
	$url=dx_remove_querystring_var($url, 'url');
	if (strrpos($url,'?')===FALSE) $url .= '?'; else $url .= '&';
	$url .= 'view=whosthere&game='.$gameid;
	return '<a rel="nofollow" href="'.$url.'">'.$prompt.'</a>';
}

function dx_thewhostherepage($gameid) {
	global $dx_embeddedplay,$dx_ad_sizes;


	list($dx_embedded_width,$dx_embedded_height)=dx_getsizes();

	list($realurl,$slot) = dx_get_game_data($gameid);

	$adsense = dx_get_enableadsense();

	$ret = '	<div id="play">
	<p><br/></p><h3>'.__("Who's there",'multiplayer').'</h3>';
	$ret .= '<iframe width="'.$dx_embedded_width." height=\"300\" scrollbars=\"auto\" scrolling=\"auto\" src=\"" . $realurl . "&amp;view=players&amp;format=extended\"></iframe>";

	if ($dx_embeddedplay) {
		$link = dx_generate_game_url($gameid);
	} else {
		$link = dx_generate_game_real_url($realurl);
	}

	$ret .= "<div class=\"playlink\">$link".__('Play Now!','multiplayer')."</a></div><!-- playlink class -->";

	if ($adsense) {

		$ret .= '<p>'.dx_adsense($dx_ad_sizes[dx_get_playsize()],dx_get_adsensepubid());
		$ret .= '</p>';
	}

	$ret .= '</div><!-- closes DIV id=whosthere -->';
	return $ret;
}

function dx_theembeddedplaypage($gameid) {
	global $dx_ad_sizes;
	
	list($dx_embedded_width,$dx_embedded_height)=dx_getsizes();

	list($realurl,$slot) = dx_get_game_data($gameid);

	$ret = '<div id="play">';

	if ($realurl) {
		$ret .= '<iframe frameborder="0" width="'.$dx_embedded_width.'" height="'.$dx_embedded_height.'" src="'.$realurl.'&width='.$dx_embedded_width.'&height='.$dx_embedded_height.'&locked=1" ></iframe>'."\n";
	} else {
		$ret .= "No URL for game $gameid";
	}
	
	$fullscreen = dx_get_enablefullscreen();
	$adsense = dx_get_enableadsense();

	if ($fullscreen || $adsense) {
		$ret .= '<div class="dx_playhelpers">'."\n";	
		if ($fullscreen) {
			$link = dx_generate_game_real_url($realurl);
			$ret .= '<p>'.$link.__('Play Fullscreen').'</a>';
			$ret .= '</p>';
		}
		if ($adsense) {
			$ret .= '<p>'.dx_adsense($dx_ad_sizes[dx_get_playsize()],dx_get_adsensepubid());
			$ret .= '</p>';
		}
		$ret .= '</div>';
	}
	

	$ret .= '</div><!-- closes DIV id=play -->';
	return $ret;
}

function dx_selfURL() { 
 $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; 
 $protocol = dx_strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; 
 $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); 
 return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']; 
} 

function dx_strleft($s1, $s2) { 
 return substr($s1, 0, strpos($s1, $s2)); 
}

function dx_remove_querystring_var($url, $key) { 
  $url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&'); 
  $url = substr($url, 0, -1); 
  return $url; 
}

function dx_get_game_data($gameid) {
	global $wpdb;
	
	$sql = "SELECT g.*,s.url FROM $wpdb->dxgames g,$wpdb->dxservers s WHERE g.id='$gameid' AND s.id=g.server;";

    $results = $wpdb->get_results($sql);
	
    if ($results && ($result=$results[0])) {
	    $gameurl = $result->url."?game=".$result->slot;
    }
    
	
	return array($gameurl,$row['slot']);
}

// Gets the URL for embedded play in wordpress site
function dx_getembeddedplayurl($gameid) {

	$url=dx_selfURL();
	$url=dx_remove_querystring_var($url, 'view');
	if ($gameid) {
		$url=dx_remove_querystring_var($url, 'game');
	}
	if (strrpos($url,'?')===FALSE) $url .= '?'; else $url .= '&';

	if ($gameid) {
		$url.="game=$gameid&";
	}
	$url .= 'view=play&locked=1';

	return $url;
}

function dx_pluginsettings_title($title=null,$icon=null) {
	echo '<h2>Multiplayer Plugin'.($title?" - $title":'').'</h2>'."\n";
	echo "<br/>\n";
}

function dx_getsizesselect($default=NULL) {
	global $dx_sizes;
	
	echo '<select name="playsize">'."\n";
	foreach ($dx_sizes as $code=>$descr) {
		echo '<option '.(($default==$code)?'selected="selected"':'').' value="'.$code.'">'.$descr.' ('.$code.')</option>'."\n";
	}
	echo '</select>'."\n";
}

function dx_get_playsize() {
	global $dx_sizes;
	$dx_playsize = get_option('dx_play_size');
	if (!$dx_playsize || !in_array($dx_playsize,array_keys($dx_sizes))) {
		$dx_playsize='640x480';
	}
	return $dx_playsize;
}


function dx_get_adsensepubid() {
	$dx_adsensepubid = get_option('dx_adsensepubid');
	if (!$dx_adsensepubid) {
		$dx_adsensepubid='pub-3542242647736863';
	}
	return $dx_adsensepubid;
}

function dx_get_enablefullscreen() {
	$dx_enablefullscreen = get_option('dx_enablefullscreen');
	if (!$dx_enablefullscreen) {
		$dx_enablefullscreen='';
	}
	return $dx_enablefullscreen;
}

function dx_get_enableadsense() {
	$dx_enableadsense = get_option('dx_enableadsense');
	if (!$dx_enableadsense) {
		$dx_enableadsense='';
	}
	return $dx_enableadsense;
}

function dx_getsizes() {
	return explode('x',dx_get_playsize());
}

function dx_adsense($type,$dx_adsense_id) {
	global $dx_adsense_displayed; // Keep count

	$dx_adsense_displayed=1+$dx_adsense_displayed;
	
	if ($dx_adsense_displayed>=3) return ''; // MAX 3 ads!
	
	
	switch ($type) {

		case '728x90':
		case '250x250':
		case '160x600':
		case '468x60':
		
		$dims = explode('x',$type);
		
		$ret= '<script type="text/javascript">google_ad_client = "'.$dx_adsense_id.'";google_ad_width = '.$dims[0].';google_ad_height = '.$dims[1].';google_ad_format = "'.$type.'_as";google_ad_type = "image";//--></script><script type="text/javascript"  src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>';
		break;
		default:
		$ret = 'Invalid adtype code: '.$type;

	}
	return $ret;

}


function dx_head() {
	echo dx_thescripts();
}

// Register hooks
add_action('admin_menu', 'dx_register');
add_action('plugins_loaded', 'dx_initialize');
add_filter('the_content', 'dx_contentfilter',5);
add_action('wp_head', 'dx_head');
?>