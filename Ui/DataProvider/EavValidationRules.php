<?php
namespace OAG\Blog\Ui\DataProvider;

use OAG\Blog\Api\Data\EavAttributeInterface;

/**
 * Class build validation rules for OAG Blog EAV attributes
 *
 * Based on Magento\Catalog\Ui\DataProvider\CatalogEavValidationRules;
 */
class EavValidationRules
{
    /**
     * Build validation rules
     *
     * @param EavAttributeInterface $attribute
     * @param array $data
     * @return array
     */
    public function build(EavAttributeInterface $attribute, array $data)
    {
        $rules = [];
        if (!empty($data['required'])) {
            $rules['required-entry'] = true;
        }
        if ($attribute->getFrontendInput() === 'price') {
            $rules['validate-zero-or-greater'] = true;
        }

        $validationClasses = $attribute->getFrontendClass()
            ? explode(' ', $attribute->getFrontendClass())
            : [];

        foreach ($validationClasses as $class) {
            if (preg_match('/^maximum-length-(\d+)$/', $class, $matches)) {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
                $rules = array_merge($rules, ['max_text_length' => $matches[1]]);
                continue;
            }
            if (preg_match('/^minimum-length-(\d+)$/', $class, $matches)) {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
                $rules = array_merge($rules, ['min_text_length' => $matches[1]]);
                continue;
            }

            $rules = $this->mapRules($class, $rules);
        }

        return $rules;
    }

    /**
     * Map classes w. rules
     *
     * @param string $class
     * @param array $rules
     * @return array
     */
    protected function mapRules($class, array $rules)
    {
        switch ($class) {
            case 'validate-number':
            case 'validate-digits':
            case 'validate-email':
            case 'validate-url':
            case 'validate-trailing-hyphen':
            case 'validate-alpha':
            case 'validate-alphanum':
                $rules = array_merge($rules, [$class => true]);
                break;
        }

        return $rules;
    }
}
