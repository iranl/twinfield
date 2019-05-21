<?php

namespace PhpTwinfield\Fields\Dimensions\Level2\Customer;

use PhpTwinfield\Article;

/**
 * The article
 * Used by: Customer
 *
 * @package PhpTwinfield\Traits
 */
trait DiscountArticleField
{
    /**
     * @var Article|null
     */
    private $discountArticle;

    public function getDiscountArticle(): ?Article
    {
        return $this->discountArticle;
    }

    public function getDiscountArticleToString(): ?string
    {
        if ($this->getDiscountArticle() != null) {
            return $this->discountArticle->getCode();
        } else {
            return null;
        }
    }

    /**
     * @return $this
     */
    public function setDiscountArticle(?Article $discountArticle): self
    {
        $this->discountArticle = $discountArticle;
        return $this;
    }

    /**
     * @param string|null $discountArticleString
     * @return $this
     * @throws Exception
     */
    public function setDiscountArticleFromString(?string $discountArticleString)
    {
        $discountArticle = new Article();
        $discountArticle->setCode($discountArticleString);
        return $this->setDiscountArticle($discountArticle);
    }
}
