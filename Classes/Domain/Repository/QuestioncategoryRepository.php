<?php
/**
 * Build up the Question category
 *
 * @author     Tim Lochmüller
 */

namespace HDNET\HdnetFaq\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Build up the Question category
 */
class QuestioncategoryRepository extends AbstractRepository {

	/**
	 * Default sorting
	 *
	 * @var array
	 */
	protected $defaultOrderings = array('sorting' => QueryInterface::ORDER_ASCENDING);

	/**
	 * Create query
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function createQuery() {
		$query = parent::createQuery();
		$query->getQuerySettings()
			->setRespectStoragePage(FALSE);
		return $query;
	}

	/**
	 * Find the categories in the right order (default: default, $sorting=TRUE: alphabetical)
	 *
	 * @param int     $topCategory
	 * @param boolean $sorting
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByParent($topCategory, $sorting = FALSE) {
		$query = $this->createQuery();
		$query->matching($query->equals('parent', $topCategory));
		if ($sorting) {
			$query->setOrderings(array('title' => QueryInterface::ORDER_ASCENDING));
		}
		return $query->execute();
	}

}
