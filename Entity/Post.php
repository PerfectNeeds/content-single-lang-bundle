<?php

namespace PN\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class Post {

    /**
     * @ORM\Column(name="content", type="json_array")
     */
    protected $content = [
        'brief' => '',
        'description' => '',
    ];

    /**
     * @ORM\ManyToMany(targetEntity="\PN\MediaBundle\Entity\Image", inversedBy="posts", cascade={"persist", "remove" })
     * @ORM\OrderBy({"tarteb" = "ASC"})
     */
    protected $images;

    /**
     * Constructor
     */
    public function __construct() {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getRelationalEntity() {
        $excludeMethods = ['id', 'content', 'images', "__initializer__", "__isInitialized__", "__cloner__"];

        $allObjects = get_object_vars($this);
        foreach ($allObjects as $objectName => $objectValue) {
            if (in_array($objectName, $excludeMethods)) {
                continue;
            }
            if ($objectValue != NULL) {
                return $objectValue;
            }
        }
        return NULL;
    }

    public function getRelationalEntityId() {
        $relationalEntity = $this->getRelationalEntity();
        if ($relationalEntity !== null) {
            return $relationalEntity->getId();
        }
        return NULL;
    }

    /**
     * Get Main Image
     *
     * @return \PN\MediaBundle\Entity\Image
     */
    public function getMainImage() {
        return $this->getImages(array(\PN\MediaBundle\Entity\Image::TYPE_MAIN))->first();
    }

    /**
     * Get Image By Type
     *
     * @return \PN\MediaBundle\Entity\Image
     */
    public function getImageByType($type) {
        return $this->getImages(array($type))->first();
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages($types = null) {
        if ($types) {
            return $this->images->filter(function(\PN\MediaBundle\Entity\Image $image) use ($types) {
                        return in_array($image->getImageType(), $types);
                    });
        } else {
            return $this->images;
        }
    }

    /**
     * Set content
     *
     * @param array $content
     *
     * @return Post
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return array
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Add image
     *
     * @param \PN\MediaBundle\Entity\Image $image
     *
     * @return Post
     */
    public function addImage(\PN\MediaBundle\Entity\Image $image) {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \PN\MediaBundle\Entity\Image $image
     */
    public function removeImage(\PN\MediaBundle\Entity\Image $image) {
        $this->images->removeElement($image);
    }

}
