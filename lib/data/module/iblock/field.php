<? namespace Intervolga\Migrato\Data\Module\Iblock;

use Intervolga\Migrato\Data\BaseUserField;
use Intervolga\Migrato\Data\Link;
use Intervolga\Migrato\Data\Record;
use Intervolga\Migrato\Data\RecordId;

class Field extends BaseUserField
{
	public function getFilesSubdir()
	{
		return "/type/";
	}

	/**
	 * @param string $userFieldEntityId
	 *
	 * @return int
	 */
	public function isCurrentUserField($userFieldEntityId)
	{
		return preg_match("/^IBLOCK_[0-9]+_SECTION$/", $userFieldEntityId);
	}

	public function getDependencies()
	{
		return array(
			"IBLOCK_ID" => new Link(Iblock::getInstance()),
		);
	}

	/**
	 * @param array $userField
	 *
	 * @return Record
	 */
	protected function userFieldToRecord(array $userField)
	{
		if ($userField["FIELD_NAME"] == "UF_XML_ID")
		{
			return null;
		}
		$record = parent::userFieldToRecord($userField);
		$hlBlockId = str_replace("IBLOCK_", "", $userField["ENTITY_ID"]);
		$hlBlockRecordId = RecordId::createNumericId($hlBlockId);
		$hlBlockXmlId = Iblock::getInstance()->getXmlIdProvider()->getXmlId($hlBlockRecordId);

		$dependency = clone $this->getDependency("IBLOCK_ID");
		$dependency->setValue($hlBlockXmlId);
		$record->addDependency("IBLOCK_ID", $dependency);

		return $record;
	}

	public function getList(array $filter = array())
	{
		if ($filter["IBLOCK_ID"])
		{
			$filter["ENTITY_ID"] = "IBLOCK_" . $filter["IBLOCK_ID"] . "_SECTION";
			unset($filter["IBLOCK_ID"]);
		}

		return parent::getList($filter);
	}

	public function update(Record $record)
	{
		$fields = $record->getFieldsStrings();

		if($iblockIdXml = $record->getDependency("IBLOCK_ID"))
		{
			$iblockId = Iblock::getInstance()->findRecord($iblockIdXml->getValue())->getValue();
			//dump($record->getId()->getValue());
			//dump($iblockId);
			dump($iblockId);
			$fields["SETTINGS"] = $this->fieldsToArray($fields, "SETTINGS", true);
			foreach($this->getLangFieldsNames() as $lang)
			{
				$fields[$lang] = $this->fieldsToArray($fields, $lang, true);
			}
			dump($fields);
		}
		parent::update($record); // TODO: Change the autogenerated stub
	}

	public function create(Record $record)
	{
		parent::create($record); // TODO: Change the autogenerated stub
	}

	public function delete($xmlId)
	{
		parent::delete($xmlId); // TODO: Change the autogenerated stub
	}
}