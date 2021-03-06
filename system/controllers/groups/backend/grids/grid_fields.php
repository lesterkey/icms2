<?php

function grid_fields($controller){

    $options = array(
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => true,
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30
        ),
        'title' => array(
            'title'    => LANG_CP_FIELD_TITLE,
            'href'     => href_to($controller->root_url, 'fields_edit', array('{id}')),
            'editable' => array(
                'table' => '{users}_fields'
            )
        ),
        'fieldset' => array(
            'title'   => LANG_CP_FIELD_FIELDSET,
            'width'   => 150,
            'handler' => function($value, $row) {
                return $value ? $value : '&mdash;';
            }
        ),
        'is_in_list' => array(
            'title' => LANG_CP_FIELD_IN_LIST_SHORT,
            'flag'  => true,
			'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', 'groups_fields', 'is_in_list')),
            'width' => 60,
        ),
        'is_in_item' => array(
            'title' => LANG_CP_FIELD_IN_ITEM_SHORT,
            'flag'  => true,
			'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', 'groups_fields', 'is_in_item')),
            'width' => 60,
        ),
        'is_in_closed' => array(
            'title' => LANG_CP_FIELD_IS_IN_CLOSED,
            'flag'  => true,
			'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', 'groups_fields', 'is_in_closed')),
            'width' => 170,
            'handler' => function($value, $row) {
                return !$row['is_system'] ? $value : '';
            }
        ),
        'name' => array(
            'title' => LANG_SYSTEM_NAME,
            'width' => 130
        ),
        'handler_title' => array(
            'title' => LANG_CP_FIELD_TYPE,
            'width' => 130
        ),
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'fields_edit', array('{id}'))
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'fields_delete', array('{id}')),
            'confirm' => LANG_CP_FIELD_DELETE_CONFIRM,
            'handler' => function($row) {
                return !$row['is_fixed'];
            }
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
