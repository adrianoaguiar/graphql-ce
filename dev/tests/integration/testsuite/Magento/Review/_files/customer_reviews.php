<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

\Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea(
    \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
);

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';

$storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    \Magento\Store\Model\StoreManagerInterface::class
)->getStore()->getId();

$reviewData = [
    [
        'customer_id' => null,
        'title' => 'GraphQl: Empty Customer Review Summary',
        'detail' => 'Review text',
        'nickname' => 'Nickname',
        'status_id' => \Magento\Review\Model\Review::STATUS_PENDING,
    ],
    [
        'customer_id' => null,
        'title' => 'GraphQl: Approved Empty Customer Review Summary',
        'detail' => 'Review text',
        'nickname' => 'Nickname',
        'status_id' => \Magento\Review\Model\Review::STATUS_APPROVED,
    ],
    [
        'customer_id' => $customer->getId(),
        'title' => 'GraphQl: Not Approved Review Summary',
        'detail' => 'Review text',
        'nickname' => 'Nickname',
        'status_id' => \Magento\Review\Model\Review::STATUS_NOT_APPROVED,
    ],
    [
        'customer_id' => $customer->getId(),
        'title' => 'GraphQl: Approved Review Summary',
        'detail' => 'Review text',
        'nickname' => 'Nickname',
        'status_id' => \Magento\Review\Model\Review::STATUS_APPROVED,
    ],
    [
        'customer_id' => $customer->getId(),
        'title' => 'GraphQl: Secondary Approved Review Summary',
        'detail' => 'Review text',
        'nickname' => 'Nickname',
        'status_id' => \Magento\Review\Model\Review::STATUS_APPROVED,
    ],
    [
        'customer_id' => $customer->getId(),
        'title' => 'GraphQl: Pending Review Summary',
        'detail' => 'Review text',
        'nickname' => 'Nickname',
        'status_id' => \Magento\Review\Model\Review::STATUS_PENDING,
    ],
];

/** @var \Magento\Review\Model\Rating $rating */
$rating = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Review\Model\Rating::class
)->getCollection()
    ->setPageSize(1)
    ->setCurPage(4)
    ->getFirstItem();

$rating->setStores([$storeId])->setIsActive(1)->save();

/** @var \Magento\Review\Model\Rating\Option $ratingOption */
$ratingOption = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Magento\Review\Model\Rating\Option::class)
    ->getCollection()
    ->setPageSize(1)
    ->setCurPage(3)
    ->addRatingFilter($rating->getId())
    ->getFirstItem();

foreach ($reviewData as $data) {
    $review = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
        \Magento\Review\Model\Review::class,
        ['data' => $data]
    );

    $review
        ->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
        ->setEntityPkValue($product->getId())
        ->setStoreId(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId()
        )
        ->setStores([
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId()
        ])
        ->save();

    $rating
        ->setReviewId($review->getId())
        ->addOptionVote($ratingOption->getId(), $product->getId());
}