<?php

namespace Newelement\Neutrino\Traits;

use Newelement\Neutrino\Models\CfGroups;
use Newelement\Neutrino\Models\CfFields;
use Newelement\Neutrino\Models\CfRule;
use Newelement\Neutrino\Models\CfObjectData;

trait CustomFields
{
	public function getFieldGroups($objectType = 'entries', $objectCategory = false, $objectId = 0)
	{
		$where = [
			'rule_category' => $objectType,
			'rule_category_specific' => '*'
		];

		$orWhere = [];

		if( $objectType === 'entries' && $objectCategory ){
			$where['rule_category_type'] = $objectCategory;
		}

		if( $objectType === 'taxonomy' && $objectCategory ){
			$where['rule_category_type'] = $objectCategory;
		}

		if( $objectId && !$objectCategory ){
			$orWhere = ['object_id' => $objectId];
		}

		$fieldGroups = CfRule::join('cf_groups AS g', 'g.id', '=', 'cf_rules.group_id')
								->where($where)
								->orWhere($orWhere)
								->get();

		$i = 0;
		foreach( $fieldGroups as $group ){
			$fieldGroups[$i]->fields = CfFields::where('group_id', $group->id)->whereNull('repeater_id')->orderBy('sort', 'asc')->get();
			$i++;
		}

		return $fieldGroups;
	}

	private function parseCustomFields($customFields, $objectId, $objectType, $object = null)
	{

		foreach( $customFields as $key => $value ){
			$cfField = CfFields::where('field_id', $key)->first();

			if( $cfField ){

				$fieldType = $cfField->field_type;
				$fieldName = $cfField->field_name;

				switch( $fieldType ){
    				case 'editor' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'editor');

					break;

					case 'text' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'text');

					break;

                    case 'textarea' :

                        $this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'textarea');

                    break;

					case 'checkbox' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'text');

					break;

					case 'file' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'file');

					break;

					case 'image' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'image');

					break;

					case 'email' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'email');

					break;

					case 'date' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'date');

					break;

					case 'number' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'number');

					break;

					case 'decimal' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'decimal');

					break;

					case 'select' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'select');

					break;

					case 'radio' :

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'radio');

					break;

					case 'repeater':

						$this->_createUpdateFieldObjectData($key, $fieldName, $objectId, $value, $objectType, $object, 'repeater');

					break;
				}

			}
		}
	}

	private function _createUpdateFieldObjectData($fieldId, $fieldName, $objectId, $value, $objectType, $object, $type)
	{

		$update = [
			'field_type' => $type,
			'field_name' => $fieldName
		];

		switch($type){
			case 'text' :
				$update['field_text'] = $value;
            break;
			case 'email' :
				$update['field_text'] = $value;
            break;
			case 'file' :
			    $update['field_file'] = $value;
			break;
			case 'image' :
				$update['field_image'] = $value;
			break;
			case 'select' :
				$update['field_text'] = implode(',', $value);
			break;
			case 'checkbox' :
				$update['field_text'] = implode(',', $value);
			break;
			case 'number' :
				$update['field_number'] = $value;
			break;
			case 'decimal' :
				$update['field_decimal'] = $value;
			break;
			case 'radio' :
				$update['field_text'] = $value;
			break;
			case 'textarea' :
				$update['field_text'] = $value;
			break;
			case 'editor' :
				$update['field_editor'] = $value;
			break;
			case 'repeater' :

			foreach( (array) $value as $repeaterKey => $row){

				$repeaterValue = '';

				$fieldData = CfFields::where('field_id', $repeaterKey)->first();

				$i = 0;

				foreach( $row as $batch => $userField ){
					$contentId = key($userField);
					$repeaterValue = $userField[key($userField)];

					$update = [];
					$batchId = $batch;

					if( $fieldData ){
						$field_type = ($fieldData->field_type === 'email')? 'text' : $fieldData->field_type;
						if( $fieldData->field_type === 'select' ){
							$field_type = 'text';
							$repeaterValue = implode(',', $repeaterValue);
						}
						if( $fieldData->field_type === 'radio' ){
							$field_type = 'text';
						}
						if( $fieldData->field_type === 'checkbox' ){
							$field_type = 'text';
							$repeaterValue = implode(',', $repeaterValue);
						}
						if( $fieldData->field_type === 'textarea' ){
							$field_type = 'text';
						}
						if( $fieldData->field_type === 'file' ){
							$field_type = 'file';
							$repeaterValue = $repeaterValue;
						}
						if( $fieldData->field_type === 'image' ){
							$field_type = 'image';
							$repeaterValue = $repeaterValue;
						}
						$update['field_'.$field_type] = $repeaterValue;
						$update['field_type'] = $fieldData->field_type;
						$update['field_name'] = $fieldData->field_name;
						$update['batch_sort'] = $i;
					}

					$fields = [
						'object_id' => $objectId,
						'field_id' => $repeaterKey,
						'object_type' => $objectType,
						'object' => $object,
						'parent_field_id' => $fieldId,
						'batch_id' => $batchId,
						'content_id' => $contentId
					];

					CfObjectData::updateOrCreate(
						$fields,
						$update
					);

					$i++;
				}

			}

			$update = [
				'field_type' => $type,
				'field_name' => $fieldName
			];

			break;
		}

		$parentData = CfObjectData::updateOrCreate(
			[ 'object_id' => $objectId, 'field_id' => $fieldId, 'object_type' => $objectType, 'object' => $object ],
			$update
		);

	}
}
