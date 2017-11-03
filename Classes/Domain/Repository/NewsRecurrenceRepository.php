<?php

namespace FalkRoeder\DatedNews\Domain\Repository;

/***
 *
 * This file is part of the "Dated News" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
 *
 ***/

/**
 * The repository for NewsRecurrences.
 */
class NewsRecurrenceRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    ];

    public function initializeObject()
    {
        $this->defaultQuerySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $this->defaultQuerySettings->setIgnoreEnableFields(true);
        $this->defaultQuerySettings->setEnableFieldsToBeIgnored(['hidden', 'deleted']);
        $this->defaultQuerySettings->setRespectStoragePage(FALSE);
    }

    /**
     * @param $id
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function getByParentId($id)
    {
        $query = $this->createQuery();
        $sql = 'SELECT r.* FROM tx_datednews_domain_model_newsrecurrence r
inner join tx_datednews_news_newsrecurrence_mm rn on rn.uid_foreign = r.uid
inner join tx_news_domain_model_news n on rn.uid_local = n.uid
where n.uid = '.$id;

        $query->statement($sql);

        return $query->execute();
    }

    /**
     * @param $dates
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function getBetweenDates($dates)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr(
                $query->logicalAnd(
                    $query->lessThan('eventstart', $dates[0]->getTimestamp()),
                    $query->greaterThan('eventend', $dates[0]->getTimestamp())
                ),
                $query->logicalAnd(
                    $query->greaterThanOrEqual('eventstart', $dates[0]->getTimestamp()),
                    $query->lessThanOrEqual('eventend', $dates[1]->getTimestamp())
                ),
                $query->logicalAnd(
                    $query->lessThan('eventstart', $dates[1]->getTimestamp()),
                    $query->greaterThan('eventend', $dates[1]->getTimestamp())
                ),
                $query->logicalAnd(
                    $query->lessThan('eventstart', $dates[0]->getTimestamp()),
                    $query->greaterThan('eventend', $dates[1]->getTimestamp())
                )
            )
        );

        return $query->execute();
    }


}
