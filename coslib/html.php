<?php

/**
 * File containing class for building forms and common methods used 
 * when building forms.
 * 
 * @package coslib 
 */

/**
 * Class used when building forms and various methods used when creating forms
 * for escaping in different ways. 
 * @package coslib
 */

class HTML {

    /**
     * array holding values used when creating forms
     * @var array $values the array of values
     */
    public static $values = array();
    
    /**
     * string holding a form  being built. 
     * @var string $formStr the form string
     */
    public static $formStr = '';
    
    /**
     * string holding a submit action which will trigger form to
     * use values being submitted
     * @var string  $autoLoadTrigger the autoLoadTrigger string
     */
    public static $autoLoadTrigger;

    /**
     * default string to seperate form fields with
     * @var string $br break string
     */
    public static $br = "<br />";
    
    /**
     * var holding all fields of a form
     * @var array $fields
     */
    public static $fields = array();
    
    /**
     * method for getting form string build. 
     * @return string $str the form build
     */
    public static function getStr () {
        $str = '';
        foreach (self::$fields as $key => $value) {
            $str.= $value['value'];
        }
        self::$fields = array();
        return $str;
    }

    /**
     * method for initing a form
     * @param array $values initial values for the form e.g. 
     *              array ('name' => 'dennis')
     * @param string $trigger the trigger value which tells the object to
     *               stop using the initial set values 
     */
    public static function init ($values = array (), $trigger = null) {
        
        if (isset($trigger)) {
            self::$autoLoadTrigger = $trigger;
        }
        
        if (!empty(self::$autoLoadTrigger)){
            $trigger = self::$autoLoadTrigger;
            if (isset($_POST[$trigger])) {
                self::$values = $_POST;
            } else if (isset($_GET[$trigger])){
                self::$values = $_GET;
            } else {
                self::$values = $values;
            }
        }
        self::$values = html::specialEncode(self::$values);
    }

    /**
     * method for disabling break between form elements
     */
    public static function disableBr (){
        self::$br = '';
    }

    /**
     * method for enabling breaks between form elements
     */
    public static function enableBr (){
        self::$br = "<br />";
    }
    
    /**
     * method for setting form values
     * @param array $values 
     */
    public static function setValues ($values) {
        self::$values = $values;
    }
    
    /**
     * method for starting a html form with options as an array
     * @param array $options array ('name' => 'test_fomr', 'enctype'=> ...
     *                              'class => 'this form', 'id => 'bla');
     * @return string $str the form start 
     */
    public static function formStartAry (
        $options = array ()) {
        if (!isset($options['enctype'])) {
            $options['enctype'] = 'multipart/form-data';
        } 
        
        if (!isset($options['name'])) {
            $options['name'] = 'form';
        }

        if (!isset($options['method'])) {
            $options['method'] = 'post';
        }
        
        
        
        $extra = self::parseExtra($options);
        if (!isset($options['action'])) {
            $extra.= "action =\"\"";
        }
        
        $str = '';
        
        //$str.= '<a class="do_hide collpase">Hide</a>&nbsp;';
        //$str.= '<a class="do_show">Show</a>';
        //$str.= "<div class=\"collapse\">\n";      
        $str.= "<form $extra>\n";
        $str.= "<fieldset>\n";
        
        self::$fields[] = array ('value' => $str);
        return $str;
    }

    /**
     * method for starting a html form
     * @param string $name name of the form
     * @param string $method method of the form
     * @param string $action action of the form
     * @param string $enctype enctype of the form
     * @return string $str the form start 
     */
    public static function formStart (
        $name = 'form', $method ='post', $action = '',
        $enctype = "multipart/form-data") {
        
        
        $str = "";
        
        //$str.= '<a class="do_hide collpase">Hide</a>&nbsp;';
        //$str.= '<a class="do_show">Show</a>';
        //$str.= "<div class=\"collapse\">\n";      
        $str.= "<form action=\"$action\" method=\"$method\" name=\"$name\" enctype = \"$enctype\">\n";
        $str.= "<fieldset>\n";
        
        self::$fields[] = array ('value' => $str);
        return $str;
    }
    
    /**
     * method for setting a legend on the form
     * @param string $legend the title of the legend
     * @param array $extra extra options to add to the legend e.g. 
     *             array ('class' => 'table-top and-more')
     * @return string $str the legend string. 
     */
    public static function legend ($legend, $extra = null){        
        $str = "<legend>$legend";
        $str.= "</legend>\n";
        self::$fields[] = array ('value' => $str);
        return $str;
    }

    /**
     * method for ending a form with the </form> tag
     * @return string $str the form end element
     */
    public static function formEnd (){
        $str = '';
        $str.= "</fieldset>\n";
        $str.= "</form>\n";
        self::$fields[] = array('value' => $str);
        return $str;
    }

    /**
     * sets a label for a form element
     * @param string $label_for the field to set the label for
     * @param string $label the label text
     * @return string $str the label
     */
    public static function label ($label_for, $label = '', $options = array()) {
        if (isset($options['required'])) {
            $label = "* " . $label;
        }
        
        if ($label_for == 'captcha') {
            // no label for images
            $str = $label. self::$br;
        } else {
            $str = "<label for=\"$label_for\">$label</label>" . self::$br . "\n";
        }
        self::$fields[] = array('value' => $str);
        return $str;
    }

    /** 
     * method for setting a value in a field
     * @access private
     * @param string $name the form field
     * @param string $value the value of the form field
     * @return string 
     */
    public static function setValue ($name, $value){
        if (isset(self::$values[$name])){
            return self::$values[$name];
        } else {
            return '';
        }
    }
    
     
    /**
     * sets a hidden element in a form. 
     * @param string $name name of the field
     * @param string $value value of the field
     * @param array  $extra elements of the field e.g. 
     *               array ('class' => 'css-test and-more')
     * @return string the hidden element (adds the element to the static form str.)  
     */
    public static function hidden ($name, $value = null, $extra = array()){
        $str = self::hiddenClean($name, $value, $extra);
        self::$fields[] = array ('value' => $str);
        return $str;
    }
    
    
    /**
     * gets a hidden field
     * @param string $name name of the field
     * @param string $value value of the field
     * @param array $extra
     * @return string $str the hidden form field 
     */
    public static function hiddenClean ($name, $value = null, $extra = array()){

        if (!isset($value)) {
            $value = self::setValue($name, $value);
        }
        
        $extra = self::parseExtra($extra);
        $str = "<input type=\"hidden\" name=\"$name\" $extra value=\"$value\" />\n";
        
        return $str;
    }
    
    /**
     * sets a text field in a form
     * @param string $name the name
     * @param string $value the value
     * @param string $extra extra e.g. array ('class' => 'css and-more')
     * @return string the text field (sets the text field in static form string)
     */
    public static function text ($name, $value = null, $extra = array()){
        $str = self::textClean($name, $value, $extra);
        self::$fields[] = array ('value' => $str);
        return $str;
    }
    
    /**
     * gets a text field as string without adding to the static form str
     * @param string $name name of the element
     * @param string $value value of the element
     * @param array $extra e.g. array ('class' => 'css-type and-more') 
     * @return string the tet field 
     */
    public static function textClean ($name, $value = null, $extra = array()){
        if (!isset($extra['size'])){
            $extra['size'] = HTML_FORM_TEXT_SIZE;
        }

        if (!isset($value)) {
            $value = self::setValue($name, $value);
        }
        
        $extra = self::parseExtra($extra);
        $str = "<input type=\"text\" name=\"$name\" $extra value=\"$value\" />" . self::$br . "\n";
        return $str;
    }

    /**
     * creates a simple captcha string (or image) 
     * @see captcha.php
     * @param string $name name of the element
     * @param string $value vlaue of the element
     * @param array $extra extras e.g. array ('class' => 'css and-more')
     * @return string $str the simple captcha input string
     */
    public static function simpleCaptcha ($name, $value = '', $extra = array()){
        if (!isset($extra['size'])){
            $extra['size'] = HTML_FORM_TEXT_SIZE;
        }

        $value = self::setValue($name, $value);
        $extra = self::parseExtra($extra);
        $str = "<input type=\"text\" name=\"$name\" $extra value=\"$value\" />" . self::$br . "\n";
        self::$fields[] = array ('value' => $str);
        return $str;
    }

    /**
     * sets form string with a password field
     * @param type $name name of the form field
     * @param type $value value of the form field. 
     * @param type $extra e.g. array ('class' => 'css and-more');
     * @return string $str the password input string
     */
    public static function password ($name, $value = '', $extra = array()){
        if (!isset($extra['size'])){
            $extra['size'] = HTML_FORM_TEXT_SIZE;
        }

        $value = self::setValue($name, $value);
        $extra = self::parseExtra($extra);
        $str = "<input type=\"password\" name=\"$name\" $extra value=\"$value\" />" . self::$br . "\n";
        self::$fields[] = array ('value' => $str);
        return $str;
    }

    /**
     * method for getting a textarea 
     * @param string $name the name of the textarea
     * @param string $value the initial value of the textarea
     * @param array $extra, e.g. css array ('class' => 'required'); 
     * @return string $str the textarea 
     */
    public static function textarea ($name, $value = null, $extra = array()){
        if (!isset($extra['rows'])){
            $extra['rows'] = HTML_FORM_TEXTAREA_HT;
        }

        if (!isset($extra['cols'])){
            $extra['cols'] = HTML_FORM_TEXTAREA_WT;
        }

        if (isset($extra['filter_help'])) {
            echo $extra['title'] = moduleLoader::getFiltersHelp($extra['filter_help']);            
        } 
        
        if (!isset($value)) {
            $value = self::setValue($name, $value);
        } 

        //$value = self::setValue($name, $value);
        $extra = self::parseExtra($extra);
        $str =  "<textarea name=\"$name\" $extra>$value</textarea>" . self::$br . "\n";
        self::$fields[] = array ('value' => $str);
        return $str;
    }
    
    /**
     * method for getting a small textarea (~1/6 of normal textarea) 
     * @param string $name the name of the textarea
     * @param string $value the initial value of the textarea
     * @param array $extra, e.g. css array ('class' => 'required'); 
     * @return string $str the textarea 
     */
    public static function textareaSmall ($name, $value = null, $extra = array()){
        if (!isset($extra['rows'])){
            $extra['rows'] = (int)(HTML_FORM_TEXTAREA_HT / 6);
        }

        self::textarea($name, $value, $extra);
    }
    
    /**
     * method for getting a medium textarea ~1/2 of normal textarea size 
     * @param string $name the name of the textarea
     * @param string $value the initial value of the textarea
     * @param array $extra, e.g. css array ('class' => 'required'); 
     * @return string $str the textarea 
     */
    public static function textareaMed ($name, $value = null, $extra = array()){
        if (!isset($extra['rows'])){
            $extra['rows'] = (int)(HTML_FORM_TEXTAREA_HT / 2);
        }

        self::textarea($name, $value, $extra);
    }

    /**
     * method for getting a file input field
     * @param string $name the name of the file field
     * @param array $extra, e.g. css array ('class' => 'popup'); 
     * @return string $str the file input field 
     */
    public static function file ($name, $extra = array()) {
        if (!isset($extra['size'])){
            $extra['size'] = HTML_FORM_TEXT_SIZE;
        }

        //$value = self::setValue($name, $value);
        $extra = self::parseExtra($extra);
        $str = "<input type=\"file\" name=\"$name\" size=\"30\" $extra />\n"  . self::$br . "\n";
        self::$fields[] = array ('value' => $str);
        return $str;
    }

    /**
     * method for getting a checkbox
     * @param string $name the name of the input
     * @param string $value the value of the input
     * @param array $extra set extras e.g. array ('class' => 'action')
     * @return string 
     */
    public static function checkbox ($name, $value = '1', $extra = array ()) {
        $value = self::setValue($name, $value);
        if ($value){
            $extra['checked'] = "yes";
        } 
        
        $extra = self::parseExtra($extra);
        $str = "<input type=\"checkbox\" name=\"$name\" value=\"1\" $extra />" . self::$br . "\n";
        self::$fields[] = array ('value' => $str, 'type' => 'checkbox');
        return $str;
    }

    /** 
     * method for getting a submit button
     * @param string $name the name of the button
     * @param string $value the value of the button
     * @param array $extra e.g. for setting css array ('class' => 'onPress')
     * @return string $str the submit button 
     */
    public static function submit ($name, $value, $extra = array ()) {
        $extra = self::parseExtra($extra);
        $str =  "<input type=\"submit\" $extra name=\"$name\" value=\"$value\" />" . self::$br . "";
        self::$fields[] = array ('value' => $str);
        return $str;
    }
    
    /**
     * add html as a field inside the form
     * @param string $str the string to add 
     */
    public static function addHtml ($str) {
        self::$fields[] = array('value'=> $str);
    }

    /**
     * method for passing extras to all form fields
     * @param array $extra e.g. array ('class' => 'required')
     * @return string $the extras parsed to a string e.g. class = "required"  
     */
    public static function parseExtra ($extra = array()) {
        $str = '';
        if (empty($extra)) return '';
        
        foreach ($extra as $key => $val){
            $str.= " $key = \"$val\" ";
        }
        return $str;
    }


    /**
     * method for making a drop down box. This will be colleted in the HTML::fields[]
     * If you just want a select box use selectClean
     * @param   string  $name the name of the select field
     * @param   array   $rows the rows making up the ids and names of the select field
     * @param   string  $field array field which will be used as name of the select element
     * @param   int     $id the array field which will be used as id of the select element
     * @param   int     $selected the element which will be selected
     * @return  string  $extras to be added to a form
     */
    public static function select($name, $rows, $field, $id, $value=null, $extra = array(), $init = array()){        
        $dropdown = self::selectClean($name, $rows, $field, $id, $value, $extra, $init);
        $str = $dropdown . self::$br . "\n" ;
        self::$fields[] = array ('value' => $str);
        return $str;
    }
    
    /**
     * method for adding a drop down box to the form.
     * 
     * @param   string  $name the name of the select field
     * @param   array   $rows the rows making up the ids and names of the select field
     * @param   string  $field array field which will be used as name of the select element
     * @param   int     $id the array field which will be used as id of the select element
     * @param   int     $selected the element which will be selected
     * @param   string  $extras to be added to this form field, e.g. css (array ('class' => 'required'));
     * @return  string  $dropdown the dropwdown string.
     * 
     */
    public static function selectClean($name, $rows, $field, $id, $value = null, $extra = array(), $init = array()){        
        $extra = self::parseExtra($extra);
        $dropdown = "<select name=\"$name\" $extra";

        if (!isset($value)) {
            $value = self::setValue($name, $value);
        }
        
        $dropdown.= ">\n";
        if (!empty($init)) {
            $dropdown.= '<option value="'.$init[$id].'"' . '' . '>'.$init[$field].'</option>'."\n";
        }
        
        foreach($rows as $row){
            if ($row[$id] == $value){
                $s = ' selected';
            } else {
                $s = '';
            }

            $dropdown .= '<option value="'.$row[$id].'"' . $s . '>'.$row[$field].'</option>'."\n";
        }
        $dropdown .= "</select>\n";
        return $dropdown;
    }

    /**
     * method for creating a html link
     * @param string $url the a href attribute
     * @param string $title the value of the link
     * @param array  $options, e.g. css class or javascript actions, e.g. ('class' => 'error')
     * @return string $link the html link
     */
    public static function createLink ($url, $title, $options = array()) {
        $rewritten_url = self::getUrl($url);

        // if rewritten
        if ($rewritten_url != $url) {
            $orginal = self::getUrl($rewritten_url);
            if ($orginal == $_SERVER['REQUEST_URI']){
                if (!isset($options['class'])){
                    $options['class'] = 'current';
                }
            }
        }

        $url = $rewritten_url;
        if ($_SERVER['REQUEST_URI'] == $url){
            if (!isset($options['class'])){
                $options['class'] = 'current';
            }
        } 

        $options = self::parseExtra($options);
        $str = "<a href=\"$url\" $options>$title</a>";
        return $str;
    }

    /**
     * method for getting url fro e.g. creating a link. If rewrite manip is enabled
     * we will fetch the rewritten link from database
     * @param  string $url
     * @return string $url rewritten if rewrite url entered url exists
     */
    public static function getUrl ($url) {
       if (class_exists('rewrite_manip')) {
            $alt_uri = rewrite_manip::getRowFromRequest(html::specialDecode(rawurldecode($url)));
            if (isset($alt_uri)){
                $url = $alt_uri;
            }
        }
        return $url;
    }

    /**
     * method for creating an image html tag
     * @param string $src the source of the image
     * @param array  $options, for setting e.g. css class array ('class' => 'popup')
     * @return string $str the image tag 
     */
    public static function createImage ($src, $options = array()) {
        $options = self::parseExtra($options);
        $str = "<img src=\"$src\" $options />";
        return $str;
    }
    
    /**
     * creates an image working as a html link
     * @param string $href the href part of the link
     * @param string $image_src the image source
     * @param array $options for setting e.g. css class array ('class' => 'popup')
     * @return string $str the a href displaying an image
     */
    public static function createHrefImage($href = null, $image_src = null, $options = array()){
        $str = self::createImage($image_src, $options);
        return "<a href=\"$href\">$str</a>";
    }


    /**
     * special encodes an array or string 
     * @param mixed $values
     * @return mixed $values special encoded
     */
    public static function specialEncode(&$values){
        if (is_array($values)){
            foreach($values as $key => $val){
                if (is_array($val)) {
                    $values[$key] = self::specialEncode($val);
                } else {
                    $values[$key] = htmlspecialchars($val, ENT_COMPAT);
                }
            }
        } else if (is_string($values)) {
            $values =  htmlspecialchars($values, ENT_COMPAT);
        } else {
            $values = '';
        }
        return $values;
    }

    /**
     * special decodes array or string 
     * @param mixed $values the values to be decoded
     * @return mixed $values the values decoded 
     */
    public static function specialDecode(&$values){
        if (is_array($values)){
            foreach($values as $key => $val){
                if (is_array($val)) {
                    $values[$key] = self::specialDecode($val);
                } else {
                    $values[$key] = htmlspecialchars_decode($val, ENT_COMPAT);
                }
            }
        } else if (is_string($values)) {
            $values =  htmlspecialchars_decode($values, ENT_COMPAT);
        } else {
            $values = '';
        }
        return $values;
    }

    /**
     * encodes entites in array or string for secure display
     * @param mixed $values the var to entity encode
     * @return mixed $values the encoded var
     */
    public static function entitiesEncode(&$values){
        if (is_array($values)){
            foreach($values as $key => $val){
                if (is_array($val)) {
                    $values[$key] = self::entitiesEncode($val);
                } else {
                    $values[$key] = htmlentities($val, ENT_COMPAT, 'UTF-8');
                }
            }
        } else if (is_string($values)) {
            $values =  htmlentities($values, ENT_COMPAT, 'UTF-8');
        } else {
            $values = '';
        }
        return $values;
    }

    /**
     * decodes entites in array or string
     * @param mixed $values the vars to decode
     * @return mixed $values the decoded vars 
     */
    public static function entitiesDecode(&$values){
        if (is_array($values)){
            foreach($values as $key => $val){
                if (is_array($val)) {
                    $values[$key] = self::entitiesDecode($val);
                } else {
                    $values[$key] = html_entity_decode($val, ENT_COMPAT, 'UTF-8');
                }
            }
        } else if (is_string($values)) {
            $values =  html_entity_decode($values, ENT_COMPAT, 'UTF-8');
        } else {
            $values = '';
        }
        return $values;
    }

    /**
     * method for creating a form widget, e.g. a tag dropdown
     * @param string $class the class to generate the widget
     * @param string $method the method to use for the widget
     * @param string $name the name of the form field
     * @param string $value the init value of the form field
     * @return string $str the widget string 
     */
    public static function widget ($class, $method, $name = null, $value = null){
        moduleLoader::includeModule ($class);
        $value = self::setValue($name, $value);
        $str = $class::$method($name, $value);
        self::$fields[] = array ('value' => $str);
        return $str;
    }

    /**
     * generic method for displaying errors on form submission
     * If template method for displaying errors (view_form_errors) 
     * exists we use this 
     * @param array $errors the errors to display
     * @return string $str html with errors 
     */
    public static function errors ($errors) {
            if (function_exists('template_view_errors')) {
                view_form_errors($errors);
                return;
            }
            if (is_string($errors)){
                echo "<!-- view_error -->\n";
                echo "<div class=\"form_error\">\n";
                echo "<p>$message</p></div>\n";
                return;
            }
            echo "<!-- view_form_errors -->\n";
            echo "<div class=\"form_error\"><ul>\n";
            foreach($errors as $error){
                echo "<li>$error</li>\n";
            }
            echo "</ul></div>\n";
            echo "<!-- / end form_error -->\n";
            return;
    }
    
    /**
     * method for sanitizing a url real simple
     * remove / ? # - add entites for displaying the url in a link
     * without any dangers
     * @param string $url
     * @return string $url
     */
    public static function sanitizeUrlSimple ($string) {
        $strip = array('/', '?', '#');
        $sub = array ('', '', '');
        $clean = trim(str_replace($strip, $sub, strip_tags($string)));
        return $clean;
        
    }
}

/**
 * function for creating a link
 * @deprecated see html::createHrefImage()
 * @param   string  the url to create the link from
 * @param   string  the title of the link
 * @param   boolean if true we only return the url and not the html link
 * @return  string  the <code><a href='url'>title</></code> tag
 */
function create_image_link($url, $href_image, $options = null){
    
    $str = '';
    if (isset($options['alt'])) $str.= " alt = \"$options[alt]\" ";
    if (isset($options['title'])) $str.= " title = \"$options[title]\" ";
    if (isset($options['width'])) $str.= " width = \"$options[width]\" ";
    if (isset($options['height'])) $alt = $options['height'];
    return "<a href=\"$url\"><img $str src=\"$href_image\" /></a>";
}
/**
 * @deprecated see html::createImage($src)
 * @param type $href_image
 * @param type $options
 * @return type 
 */
function create_image($href_image, $options = null){  
    $str = '';
    if (isset($options['alt'])) $str.= " alt = \"$options[alt]\" ";
    if (isset($options['width'])) $str.= " width = \"$options[width]\" ";
    if (isset($options['height'])) $alt = $options['height'];
    return "<img $str src=\"$href_image\" />";
}

/**
 * function for creating a select dropdown from a database table.
 * @deprecated see html::select()
 * @param   string  the name of the select filed
 * @param   string  the database table to select from
 * @param   string  the database field which will be used as name of the select element
 * @param   int     the database field which will be used as id of the select element
 * @param   int     the element which will be selected
 * @param   array   array of other non db options
 * @param   string  behavior e.g. onChange="this.form.submit()"
 * @return  string  the select element to be added to a form
 */
function view_drop_down_db($name, $table, $field, $id, $selected=null, $extras = null, $behav = null){
    $db = new db();
    $dropdown = "<select name=\"$name\" ";
    if (isset($behav)){
        $dropdown.= $behav;
        
    }
    $dropdown.= ">\n";
    $rows = $db->selectAll($table);
    if (isset($extras)){
        $rows = array_merge($extras, $rows);
    }
    foreach($rows as $row){
        if ($row[$id] == $selected){
            $s = ' selected';
        } else {
            $s = '';
        }

        $dropdown.= '<option value="'.$row[$id].'"' . $s . '>'.$row[$field].'</option>'."\n";
    }
    $dropdown.= '</select>'."\n";
    return $dropdown;
}

/**
 * @deprecated see html::select()
 * @param   string  the name of the select field
 * @param   array   the rows making up the ids and names of the select field
 * @param   string  the field which will be used as name of the select element
 * @param   int     the field which will be used as id of the select element
 * @param   int     the element which will be selected
 * @return  string  the select element to be added to a form
 */
function view_drop_down($name, $rows, $field, $id, $selected=null, $behav = null){
    $dropdown = "<select name=\"$name\" ";
    if (isset($behav)){
        $dropdown.= $behav;

    }
    $dropdown.= ">\n";
    foreach($rows as $row){
        if ($row[$id] == $selected){
            $s = ' selected';
        } else {
            $s = '';
        }

        $dropdown .= '<option value="'.$row[$id].'"' . $s . '>'.$row[$field].'</option>'."\n";
    }
    $dropdown .= '</select>'."\n";
    return $dropdown;
}
