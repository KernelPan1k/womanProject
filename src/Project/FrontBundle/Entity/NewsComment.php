<?php

namespace Project\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Response
 *
 * @ORM\Table(name="news_comment")
 * @ORM\Entity(repositoryClass="Project\FrontBundle\Repository\NewsCommentRepository")
 */
class NewsComment extends Comment
{
    /**
     * @var News
     *
     * @ORM\ManyToOne(targetEntity="Project\FrontBundle\Entity\News", inversedBy="comments")
     */
    private $news;

    /**
     * Get news
     *
     * @return News
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @param News $news
     *
     * @return NewsComment
     */
    public function setNews(News $news):NewsComment
    {
        $this->news = $news;
        $news->addComment($this);

        return $this;
    }
}
