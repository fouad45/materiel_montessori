<?php
/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

/**
 * Html helper.
 */

class Html
{
    /**
     * @var array
     */
    public static $voidElements = array(
        'area' => 1,
        'base' => 1,
        'br' => 1,
        'col' => 1,
        'command' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'keygen' => 1,
        'link' => 1,
        'meta' => 1,
        'param' => 1,
        'source' => 1,
        'track' => 1,
        'wbr' => 1,
    );

    /**
     * @var array
     */
    public static $attributeOrder = array(
        'type',
        'id',
        'class',
        'name',
        'value',

        'href',
        'src',
        'action',
        'method',

        'selected',
        'checked',
        'readonly',
        'disabled',
        'multiple',

        'size',
        'maxlength',
        'width',
        'height',
        'rows',
        'cols',

        'alt',
        'title',
        'rel',
        'media',
    );

    /**
     * @var array
     */
    public static $dataAttributes = array('data', 'data-ng', 'ng');

    /**
     * Encodes special characters into HTML entities.
     *
     * @param  string $content
     * @param  bool   $doubleEncode
     *
     * @return string
     */
    public static function encode($content, $doubleEncode = true)
    {
        return htmlspecialchars(
            $content,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
            $doubleEncode
        );
    }

    /**
     * Decodes special HTML entities back to the corresponding characters.
     * This is the opposite of [[encode()]].
     *
     * @param  string $content
     *
     * @return string
     */
    public static function decode($content)
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }

    /**
     * Generates a complete HTML tag.
     *
     * @param  string|bool|null $name
     * @param  string           $content
     * @param  array            $options
     *
     * @return string
     */
    public static function tag($name, $content = '', $options = array())
    {
        if (null === $name || null === $name) {
            return $content;
        }

        $html = "<$name".static::renderTagAttributes($options).'>';

        return isset(static::$voidElements[Tools::strtolower($name)])
            ? $html
            : "$html$content</$name>";
    }

    /**
     * Generates a start tag.
     *
     * @param  string|bool|null $name
     * @param  array            $options
     *
     * @return string
     */
    public static function beginTag($name, $options = array())
    {
        if (null === $name || false === $name) {
            return '';
        }

        return "<$name".static::renderTagAttributes($options).'>';
    }

    /**
     * Generates an end tag.
     *
     * @param  string|bool|null $name
     *
     * @return string
     */
    public static function endTag($name)
    {
        if (null === $name || false === $name) {
            return '';
        }

        return "</$name>";
    }

    /**
     * Generates a style tag.
     *
     * @param  string $content
     * @param  array  $options
     *
     * @return string
     */
    public static function style($content, $options = array())
    {
        return static::tag('style', $content, $options);
    }

    /**
     * Generates a script tag.
     *
     * @param  string $content
     * @param  array  $options
     *
     * @return string
     */
    public static function script($content, $options = array())
    {
        return static::tag('script', $content, $options);
    }

    /**
     * Wraps given content into conditional comments for IE, e.g., `lt IE 9`.
     *
     * @param  string $content
     * @param  string $condition
     *
     * @return string
     */
    private static function wrapIntoCondition($content, $condition)
    {
        if (false !== strpos($condition, '!IE')) {
            return "<!--[if $condition]><!-->\n".$content."\n<!--<![endif]-->";
        }

        return "<!--[if $condition]>\n".$content."\n<![endif]-->";
    }

    /**
     * Generates a hyperlink tag.
     *
     * @param  string            $text
     * @param  array|string|null $url
     * @param  array             $options
     *
     * @return string
     */
    public static function a($text, $url = null, $options = array())
    {
        if (null !== $url) {
            $options['href'] = $url;
        }

        return static::tag('a', $text, $options);
    }

    /**
     * Generates a mailto hyperlink.
     *
     * @param  string $text
     * @param  string $email
     * @param  array  $options
     *
     * @return string
     */
    public static function mailto($text, $email = null, $options = array())
    {
        $options['href'] = 'mailto:'.(null === $email ? $text : $email);

        return static::tag('a', $text, $options);
    }

    /**
     * Generates an image tag.
     *
     * @param  array|string $src
     * @param  array        $options
     *
     * @return string
     */
    public static function img($src, $options = array())
    {
        $options['src'] = $src;
        if (!isset($options['alt'])) {
            $options['alt'] = '';
        }

        return static::tag('img', '', $options);
    }

    /**
     * Generates a label tag.
     *
     * @param  string $content
     * @param  string $for
     * @param  array  $options
     *
     * @return string
     */
    public static function label($content, $for = null, $options = array())
    {
        $options['for'] = $for;

        return static::tag('label', $content, $options);
    }

    /**
     * Generates a button tag.
     *
     * @param string $content
     * @param array $options
     *
     * @return string
     */
    public static function button($content = 'Button', $options = array())
    {
        if (!isset($options['type'])) {
            $options['type'] = 'button';
        }

        return static::tag('button', $content, $options);
    }

    /**
     * Generates a submit button tag.
     *
     * @param string $content
     * @param array $options
     *
     * @return string
     */
    public static function submitButton($content = 'Submit', $options = array())
    {
        $options['type'] = 'submit';

        return static::button($content, $options);
    }

    /**
     * Generates a reset button tag.
     *
     * @param string $content
     * @param array $options
     *
     * @return string
     */
    public static function resetButton($content = 'Reset', $options = array())
    {
        $options['type'] = 'reset';

        return static::button($content, $options);
    }

    /**
     * Generates an input type of the given type.
     *
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array $options
     *
     * @return string
     */
    public static function input(
        $type,
        $name = null,
        $value = null,
        $options = array()
    ) {
        if (!isset($options['type'])) {
            $options['type'] = $type;
        }
        $options['name'] = $name;
        $options['value'] = null === $value ? null : (string) $value;

        return static::tag('input', '', $options);
    }

    /**
     * Generates an input button.
     *
     * @param string $label
     * @param array $options
     *
     * @return string
     */
    public static function buttonInput($label = 'Button', $options = array())
    {
        $options['type'] = 'button';
        $options['value'] = $label;

        return static::tag('input', '', $options);
    }

    /**
     * Generates a submit input button.
     *
     * @param string $label
     * @param array $options
     *
     * @return string
     */
    public static function submitInput($label = 'Submit', $options = array())
    {
        $options['type'] = 'submit';
        $options['value'] = $label;

        return static::tag('input', '', $options);
    }

    /**
     * Generates a reset input button.
     *
     * @param string $label
     * @param array $options
     *
     * @return string
     */
    public static function resetInput($label = 'Reset', $options = array())
    {
        $options['type'] = 'reset';
        $options['value'] = $label;

        return static::tag('input', '', $options);
    }

    /**
     * Generates a text input field.
     *
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    public static function textInput($name, $value = null, $options = array())
    {
        return static::input('text', $name, $value, $options);
    }

    /**
     * Generates a hidden input field.
     *
     * @param string $name
     * @param string $value
     * @param array $options
     *
     * @return string
     */
    public static function hiddenInput($name, $value = null, $options = array())
    {
        return static::input('hidden', $name, $value, $options);
    }

    /**
     * Generates a password input field.
     *
     * @param string $name
     * @param string $value
     * @param array $options
     *
     * @return string
     */
    public static function passwordInput($name, $value = null, $options = array())
    {
        return static::input('password', $name, $value, $options);
    }

    /**
     * Generates a file input field.
     *
     * @param string $name
     * @param string $value
     * @param array $options
     *
     * @return string
     */
    public static function fileInput($name, $value = null, $options = array())
    {
        return static::input('file', $name, $value, $options);
    }

    /**
     * Generates a radio button input.
     *
     * @param string $name
     * @param bool $checked
     * @param array $options
     *
     * @return string the generated radio button tag
     */
    public static function radio($name, $checked = false, $options = array())
    {
        return static::booleanInput('radio', $name, $checked, $options);
    }

    /**
     * Generates a checkbox input.
     *
     * @param string $name
     * @param bool $checked
     * @param array $options
     *
     * @return string
     */
    public static function checkbox($name, $checked = false, $options = array())
    {
        return static::booleanInput('checkbox', $name, $checked, $options);
    }

    /**
     * Generates a boolean input.
     *
     * @param string $type
     * @param string $name
     * @param bool $checked
     * @param array $options
     *
     * @return string
     */
    protected static function booleanInput(
        $type,
        $name,
        $checked = false,
        $options = array()
    ) {
        $options['checked'] = (bool) $checked;
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            # add a hidden field so that if the checkbox is not selected,
            # it still submits a value
            $hidden = static::hiddenInput($name, $options['uncheck']);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }
        if (isset($options['label'])) {
            $label = $options['label'];
            $labelOptions = isset($options['labelOptions'])
                ? $options['labelOptions']
                : array();
            unset($options['label'], $options['labelOptions']);
            $content = static::label(
                static::input($type, $name, $value, $options).' '.$label,
                null,
                $labelOptions
            );

            return $hidden.$content;
        } else {
            return $hidden.static::input($type, $name, $value, $options);
        }
    }

    /**
     * Renders the HTML tag attributes.
     *
     * @param array $attributes
     *
     * @return string
     */
    public static function renderTagAttributes($attributes)
    {
        if (count($attributes) > 1) {
            $sorted = array();
            foreach (static::$attributeOrder as $name) {
                if (isset($attributes[$name])) {
                    $sorted[$name] = $attributes[$name];
                }
            }
            $attributes = array_merge($sorted, $attributes);
        }

        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $name";
                }
            } elseif (is_array($value)) {
                if (in_array($name, static::$dataAttributes)) {
                    foreach ($value as $n => $v) {
                        $html .= " $name-$n=\"".static::encode($v).'"';
                    }
                } elseif ('class' === $name) {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"".static::encode(
                        implode(' ', $value)
                    ).'"';
                } elseif ('style' === $name) {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"".static::encode(
                        static::cssStyleFromArray($value)
                    ).'"';
                } else {
                    $html .= " $name='".static::encode($value)."'";
                }
            } elseif ($value !== null) {
                $html .= " $name=\"".static::encode($value).'"';
            }
        }

        return $html;
    }

    /**
     * Converts a CSS style array into a string representation.
     *
     * @param array $style
     *
     * @return string
     */
    public static function cssStyleFromArray(array $style)
    {
        $result = '';
        foreach ($style as $name => $value) {
            $result .= "$name: $value; ";
        }

        # return null if empty to avoid rendering the "style" attribute
        return '' === $result ? null : rtrim($result);
    }

    /**
     * Converts a CSS style string into an array representation.
     *
     * @param string $style
     *
     * @return array
     */
    public static function cssStyleToArray($style)
    {
        $result = array();
        foreach (explode(';', $style) as $property) {
            $property = explode(':', $property);
            if (count($property) > 1) {
                $result[trim($property[0])] = trim($property[1]);
            }
        }

        return $result;
    }
}
