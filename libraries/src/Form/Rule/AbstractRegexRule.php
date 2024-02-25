<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2024 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Form\Rule;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Form\Form;
use Joomla\Registry\Registry;

/**
 * Class allowing easy implementation of regex rules for data validation.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractRegexRule implements FormRuleInterface
{
    use RuleConstraintTrait;

    /**
     * The regular expression to use in testing a form field value.
     *
     * @var    string
     * @since  __DEPLOY_VERSION__
     */
    protected string $regex;

    /**
     * The regular expression modifiers to use when testing a form field value.
     *
     * @var    string
     * @since  __DEPLOY_VERSION__
     */
    protected string $modifiers = '';

    /**
     * Method to test the value.
     *
     * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param   mixed              $value    The form field value to validate.
     * @param   ?string            $group    The field name group control value. This acts as as an array container for the field.
     *                                       For example if the field has name="foo" and the group value is set to "bar" then the
     *                                       full field name would end up being "bar[foo]".
     * @param   ?Registry          $input    An optional Registry object with the entire data set to validate against the entire form.
     * @param   ?Form              $form     The form object for which the field is being tested.
     *
     * @return  void
     *
     * @since   1.6
     * @throws  \UnexpectedValueException  If regex is invalid.
     */
    public function test(\SimpleXMLElement $element, mixed $value, string $group = null, Registry $input = null, Form $form = null): void
    {
        $this->ruleRun = true;

        // Check for a valid regex.
        if (empty($this->regex)) {
            throw new \UnexpectedValueException(sprintf('%s has invalid regex.', \get_class($this)));
        }

        // Detect if we have full UTF-8 and unicode PCRE support.
        static $unicodePropertiesSupport = null;

        if ($unicodePropertiesSupport === null) {
            $unicodePropertiesSupport = (bool) @preg_match('/\pL/u', 'a');
        }

        // Add unicode property support if available.
        if ($unicodePropertiesSupport) {
            $this->modifiers = (strpos($this->modifiers, 'u') !== false) ? $this->modifiers : $this->modifiers . 'u';
        }

        // Test the value against the regular expression.
        if (preg_match(\chr(1) . $this->regex . \chr(1) . $this->modifiers, $value)) {
            $this->isValid = true;
        }
    }
}
