<?php
/*
Plugin Name: TreeMagic-Cypress
Plugin URI: http://ambientwebs.com/?page_id=12
Version: 3.2.0
Author: Ambient Webs LLC
Description: Way of making Internet and Intranet information easily accessible
*/

if (!class_exists("MQFunctions")) {
    class MQFunctions {
        var $MQOptionsName = "TreeMagicCypressOptions";
		var $PluginName = "TM-Cypress";
		var $update_URI = "http://rodwans/wppun.txt";
		var $current_version = "3.2.0";
        function MQFunctions() { } //constructor
        function getMQOptions() {
            $MQ['displayText'] = array(0 => 'Wikipedia', 1 => 'Google', 2 => 'Google - Images', 3 => 'Google - News');
            $MQ['targetURL'] = array(0 => 'http://en.wikipedia.org/wiki/', 1 => 'http://www.google.com/search?hl=en&q=', 2 => 'http://images.google.com/images?hl=en&gbv=2&q=', 3 => 'http://news.google.com/news?hl=en&gbv=2&ie=UTF-8&sa=N&tab=in&q=');
            $MQ['targetPageOption'] = array( 0 => 'true', 1 => 'true', 2 => 'true' , 3 => 'true' );
            $MQ['parent'] = array(0 => 'root', 1 => 'root', 2 => 'http://www.google.com/search?hl=en&q=', 2 => 'http://www.google.com/search?hl=en&q=');
            $devOptions = get_option($this->MQOptionsName);
            if (!empty($devOptions)) {
                foreach($devOptions as $key => $option)
                $MQ[$key] = $option;
            }
            update_option($this->MQOptionsName, $MQ);
            return $MQ;
        }
		function tm_plugin_update_row( $file ) {
			$PluginName = $this->PluginName;
			$update_URI = $this->update_URI;
			$current_version = $this->current_version;
			if (substr($file,0,strlen($PluginName)) == $PluginName){
				if (@fopen($update_URI, "r")) {
					$_data = implode('', file($update_URI));
					preg_match("|version:(.*)|i", $_data, $_version);
					preg_match("|location:(.*)|i", $_data, $_location);
					$new_version = trim($_version[1]);
					$location = trim($_location[1]);
					if( strnatcmp($current_version,$new_version) == -1){
						echo "<tr><td colspan='5' class='plugin-update'>";
						printf( __('There is a new version of %s available. <a href="%s">Download version %s here</a>.'), $PluginName, $location, $new_version  );
						echo "</td></tr>";
					}
				}
			}
		}
        function buildMQHeader(){
            echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/TM-Cypress/TM-Cypress.css" />'. "\n";
            wp_enqueue_script('devlounge_plugin_series', get_bloginfo('wpurl') . '/wp-content/plugins/TM-Cypress/TM-Cypress.js', array('prototype'), '0.3');

			//BasheerG 2008.03.05: Supporting GreyBox feature
			echo '<script type="text/javascript">var GB_ROOT_DIR = "' . get_bloginfo('wpurl') . '/wp-content/plugins/TM-Cypress/greybox/";</script>';
			wp_enqueue_script('greybox_01', get_bloginfo('wpurl') . '/wp-content/plugins/TM-Cypress/greybox/AJS.js', array('prototype'), '0.3');
			wp_enqueue_script('greybox_02', get_bloginfo('wpurl') . '/wp-content/plugins/TM-Cypress/greybox/AJS_fx.js', array('prototype'), '0.3');
			wp_enqueue_script('greybox_03', get_bloginfo('wpurl') . '/wp-content/plugins/TM-Cypress/greybox/gb_scripts.js', array('prototype'), '0.3');
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/TM-Cypress/greybox/gb_styles.css" media="all"/>'. "\n";
        }

		function buildMQContent(){
            $devOptions = $this->getMQOptions();
            echo "  <div id=\"aw_popup\" class=\"aw_popupmenu\" style=\"visibility:hidden;\" onclick='hide_menu( 1 );' >\n";
            echo "    <ul>\n";
            echo "        <div id=\"aw_popup\" class=\"h2\">TM-Cypress Search</div>\n";
            $mainMenu = 0;
            for	( $index = 0 ; $index < sizeOf( $devOptions['displayText'] ) ; $index ++	) {
                $hasChild = 0;
                if ( $index != 0 ) {
                    if($devOptions['parent'][$index] == 'root' ){
                        if ( $mainMenu == 1 ){
                            echo "        </ul>\n";
                            echo "        </li>\n";
                            $mainMenu = 0;
                        }
                    }else{
                        if ( $mainMenu == 0 ){
                            echo "\n        <ul>\n";
                            $mainMenu = 1;
                        }
                    }
                }
                if ( $mainMenu == 0 ){
                    if ( $index + 1  < sizeOf( $devOptions['displayText'] ) ){
                        if ( $devOptions['parent'][$index + 1] != 'root' ){
                            $hasChild = 1;
                        }
                    }
                }

                echo "        <li";
                if ( $hasChild == 1 ) {
                    echo " class=\"subRight\"";
                }
                echo "><A href=\"#\" onclick='browsURL(\"" . $devOptions['targetURL'][$index] . "\",";
                if($devOptions['targetPageOption'][$index] == "true"){
                    echo "1";
                }else{
                    echo "0";
                }
                echo ");' >" . $devOptions['displayText'][$index] . "</a>";
                if ( $hasChild != 1 ) {
                    echo "</li>\n";
                }

            }
            if ( $mainMenu == 1 ){
                echo "        </ul>\n";
                echo "        </li>\n";
                $mainMenu = 0;
            }
            echo "    </ul>\n";
            echo "  </div>\n";

        }

        function viewMQOptions() {
            $devOptions = $this->getMQOptions();
            if (isset($_POST['updateMQ'])) {

                if (isset($_POST['displayText'])) {
                    $devOptions['displayText'] = $_POST['displayText'];
                }
                if (isset($_POST['targetURL'])) {
                    $devOptions['targetURL'] = $_POST['targetURL'];
                }
                if (isset($_POST['parent'])) {
                    $devOptions['parent'] = $_POST['parent'];
                }
                $devOptions['targetPageOption'] = $_POST['targetPageOption'];

                update_option($this->MQOptionsName, $devOptions);

?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "MQFunctions");?></strong></p></div>
<?php
            }
?>
<SCRIPT LANGUAGE="JavaScript">
			String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ''); };
			function addRel( node, TypeOfRelation ) {
				var MainContainer = node.parentNode;
			    if ( TypeOfRelation == 0 )
			    {
				    var  newOption = document.createElement( "div" );
				    newOption.id = 'main';
				    newOption = createOption( newOption, 'root' )

				    var forms = MainContainer.getElementsByTagName( 'div' );
				    var container = forms[0]
				    container.appendChild(newOption);

			    }else if ( TypeOfRelation == 1 )
			    {
					var inputs = MainContainer.getElementsByTagName( 'input' );
					parentName = inputs[1].value.trim();
					if ( parentName == "" )
					{
						alert( "Fill the Target URL field first " ) ;
						return;
					}
					var  newOption = document.createElement( "div" );
				    newOption.id = 'child';

				    newOption = createOption( newOption, parentName )

					if ( MainContainer.nextSibling )
					{
						MainContainer.parentNode.insertBefore(newOption,MainContainer.nextSibling)
					}else{
						MainContainer.parentNode.appendChild(newOption);
					}
			    }


			}
			function removeRel(node) {

			    var formElement = node.parentNode;

			    var container = node.parentNode.parentNode;

			    container.removeChild(formElement);
			}
			function createOption( container, parentName ){

			    var  displayObject = document.createElement("input");
			    displayObject.type = 'text' ;
			    displayObject.name = 'displayText[]' ;
			    displayObject.value = '';
			    var displayText = document.createTextNode('Display Text : ');

			    var  targetURLObject = document.createElement("input");
			    targetURLObject.type = 'text' ;
			    targetURLObject.name = 'targetURL[]' ;
			    targetURLObject.value = '';
			    var targetURLText = document.createTextNode(' Target URL : ');

			    var  targetPageViewObject = document.createElement("input");
			    targetPageViewObject.type = 'checkbox' ;
			    targetPageViewObject.name = 'targetOption' ;
			    targetPageViewObject.value = 'true';
				targetPageViewObject.onclick = new Function( 'changeTargetPage( this )');
			    var targetPageText = document.createTextNode('New Page\n');

			    var removeLink = document.createElement('a'); ;
			    removeLink.href = 'javascript:;';
			    removeLink.onclick = new Function( 'removeRel(this)');
			    removeLink.innerHTML  = '[remove]';

			    if ( parentName == 'root' )
			    {
					var addChildRelation = document.createElement('a'); ;
					addChildRelation.href = 'javascript:;';
					addChildRelation.onclick = new Function( 'addRel( this, 1 )');
				    addChildRelation.innerHTML  = 'Add Child';
			    }

				var  targetPageObject = document.createElement("input");
			    targetPageObject.type = 'hidden' ;
			    targetPageObject.name = 'targetPageOption[]' ;
			    targetPageObject.value = 'false';

				var  parentObject = document.createElement("input");
			    parentObject.type = 'hidden' ;
			    parentObject.name = 'parent[]' ;
			    parentObject.value = parentName;

			    var newLine = document.createTextNode('\n');
			    var firstSpace = document.createTextNode(' ');
				var secondSpace = document.createTextNode('  ');

			    container.appendChild( displayText );
			    container.appendChild( displayObject );
			    container.appendChild(targetURLText);
			    container.appendChild(targetURLObject);
				container.appendChild(firstSpace );
			    container.appendChild(targetPageViewObject);
			    container.appendChild(targetPageText);
				if ( parentName == 'root' )
			    {
					container.appendChild(addChildRelation);
					container.appendChild(secondSpace);
				}
			    container.appendChild(removeLink);
				container.appendChild( parentObject );
				container.appendChild( targetPageObject );
			    container.appendChild(newLine);

			    return container;
			}
			function changeTargetPage( node ) {
				var container = node.parentNode;

				var inputs = container.getElementsByTagName('input');
				for( index = 0; index < inputs.length ; index++){
					if ( inputs[index].name == 'targetPageOption[]') {
			                inputs[index].value = node.checked;
							break;
			        }
				}
			}
</SCRIPT>
<style type="text/css">
    #main{
    padding: 10px 0px 0px 0px;
    font-weight: bold;
    }
    #child{
    padding: 0px 0px 0px 10px;

    }
</style>
<div class=wrap>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <h2>TM-Cypress Options Editor</h2>
        <p>
        You can start with adding TreeMagic-Cypress options, by pressing the "<i>Add another Option</i>" button below and filling the following mandatory fields:
        <ol>
            <li>	Display Text: Contains the text that will show to the end-users.
            <li>	Target URL: Contains the URL address that the end-user will browse to upon clicking this option.
            <li>	New Page: Specifies if the related TM-Cypress option will open the "Target URL" in a new window or in the same browsing window.
        </ol>

        Additionally and for multi-level options, you can create a child menu for any item, by pressing the "[Add Child]" link and filling all the mandatory fields.

        On the other hand, you can remove any TM-Cypress option, by pressing on the "<i>[Remove]</i>" link at anytime.
        <br/><br/>
        Finally, press "<i>Update Settings</i>" button, to save and reflect all the changes that you have done.
        <br/><br/><br/>
        <div>
            <?php
            for	( $index = 0 ; $index < sizeOf( $devOptions['displayText'] ) ; $index ++	) {
        if( $devOptions['parent'][$index] == 'root' ){
            ?>
            <div id = 'main'>
                Display Text: <input type='text' name='displayText[]' value='<?php _e(apply_filters('format_to_edit',$devOptions['displayText'][$index]),'MQFunctions') ?>' />
                Target URL: <input type='text' name='targetURL[]' value='<?php _e(apply_filters('format_to_edit',$devOptions['targetURL'][$index]),'MQFunctions') ?>'/>
                <input type='checkbox' name='targetOption' onclick='changeTargetPage( this );' value="true" <?php if($devOptions['targetPageOption'][$index] == "true") { _e('checked="checked"',"MQFunctions"); }?>/>New Page
                <a href="javascript:;" onclick='addRel( this, 1 )'>[Add Child]</a>
                <a href="javascript:;" onclick="removeRel(this)" <?php if( $index == 0 ) { _e( 'style="display: none;"',"MQFunctions"); }?>>[Remove]</a>
                <input value='root' name="parent[]" type="hidden">
                <input value='<?php _e(apply_filters('format_to_edit',$devOptions['targetPageOption'][$index] ),'MQFunctions') ?>' name="targetPageOption[]" type="hidden">
            </div>
            <?php
            }else{
            ?>
            <div id='child'>
                Display Text: <input type='text' name='displayText[]' value='<?php _e(apply_filters('format_to_edit',$devOptions['displayText'][$index]),'MQFunctions') ?>' />
                Target URL: <input type='text' name='targetURL[]' value='<?php _e(apply_filters('format_to_edit',$devOptions['targetURL'][$index]),'MQFunctions') ?>'/>
                <input type='checkbox' name='targetOption' onclick='changeTargetPage( this );' value="true" <?php if($devOptions['targetPageOption'][$index] == "true") { _e('checked="checked"',"MQFunctions"); }?>/>New Page
                <a href="javascript:;" onclick="removeRel(this)" <?php if( $index == 0 ) { _e( 'style="display: none;"',"MQFunctions"); }?>>[Remove]</a>
                <input value='<?php _e(apply_filters('format_to_edit',$devOptions['parent'][$index]),'MQFunctions') ?>' name="parent[]" type="hidden">
                <input value='<?php _e(apply_filters('format_to_edit',$devOptions['targetPageOption'][$index] ),'MQFunctions')   ?>' name="targetPageOption[]" type="hidden">
            </div>
            <?php
            }
            }
            ?>
        </div>
        <input type='button' onclick='addRel( this, 0 )' value='Add another Option'/>
        <div class="submit">
            <input type="submit" name="updateMQ" value="<?php _e('Update Settings', 'MQFunctions') ?>" />
        </div>

    </form>
</div>
<?php
        }
    }
} //End

if (class_exists("MQFunctions")) {
    $dl_MQFunctions = new MQFunctions();
}
//Initialize the admin panel
if (!function_exists("JValleyPluginSeries_ap")) {
    function JValleyPluginSeries_ap() {
        global $dl_MQFunctions;
        if (!isset($dl_MQFunctions)) {
            return;
        }
        if (function_exists('add_options_page')) {
            add_options_page('TM-Cypress Options Editor', 'TM-Cypress Options Editor', 9, basename(__FILE__), array(&$dl_MQFunctions, 'viewMQOptions'));
        }
    }
}
if (isset($dl_MQFunctions)) {
//Actions
    add_action('wp_head', array(&$dl_MQFunctions, 'buildMQHeader'), 1);
	add_action( 'wp_footer', array(&$dl_MQFunctions, 'buildMQContent'), 1);

}
add_action('admin_menu', 'JValleyPluginSeries_ap');
add_action( 'after_plugin_row',  array(&$dl_MQFunctions, 'tm_plugin_update_row') );
?>
