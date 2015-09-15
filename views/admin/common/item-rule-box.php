<?php
    $db = get_db();
    $sql = "
        SELECT
            es.name AS element_set_name,
            e.id AS element_id,
            e.name AS element_name,
            it.name AS item_type_name
        FROM {$db->ElementSet} es
        JOIN {$db->Element} e ON es.id = e.element_set_id
        LEFT JOIN {$db->ItemTypesElements} ite ON e.id = ite.element_id
        LEFT JOIN {$db->ItemType} it ON ite.item_type_id = it.id
        WHERE es.record_type IS NULL OR es.record_type = 'Item'
        ORDER BY es.name, it.name, e.name
    ";
    $elements = $db->fetchAll($sql);
    $form_element_options = array();
    foreach ($elements as $element) {
        $optGroup = $element['item_type_name']
            ? __('Item Type') . ': ' . __($element['item_type_name'])
            : __($element['element_set_name']);
        $value = __($element['element_name']);

        $form_element_options[$optGroup][$element['element_id']] = $value;
    }

    $form_compare_options = array(
        'exact' => __('is exactly'),
        'contains' => __('contains'),
        '!exact' => __('is not exactly'),
        '!contains' => __('does not contain'),
        'regexp'=>__('matches regular expression')
    );
?>

<div id="item-rule-box" class="item-rule-box" style="clear:left;">
    <div class="inputs three columns alpha">
        <?php echo $this->formSelect('bulk-metadata-editor-element-id', '50', array('class' => 'bulk-metadata-editor-element-id'), $form_element_options) ?>
    </div>
    <div class="inputs two columns beta">
        <?php echo $this->formSelect('bulk-metadata-editor-compare', null, array('class' => 'bulk-metadata-editor-compare'), $form_compare_options) ?>
    </div>
    <div class="inputs three columns omega">
        <?php
            echo $this->formText(
                'bulk-metadata-editor-selector',
                null,
                array(
                    'class' => 'bulk-metadata-editor-selector',
                    'placeholder' => __('Input search term here')
                )
            );
        ?>
    </div>
    <div class="removeRule">[x]</div>
    <div class="field">
        <div class="inputs two columns omega">
            <?php echo $this->formCheckbox('bulk-metadata-editor-case',"Match Case",array('class'=>'bulk-metadata-editor-case')) ?><label for="bulk-metadata-editor-case"> <?php echo __('Match Case'); ?> </label>
        </div>
    </div>
</div>
