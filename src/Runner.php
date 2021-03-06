<?php

namespace SDC\Contao\FullBackgroundImages;

use Model\Collection;
use SDC\Contao\FullBackgroundImages\Helper\Helper;
use SDC\Contao\FullBackgroundImages\Helper\HelperInterface;

class Runner extends \Frontend {

    /**
     * @var HelperInterface
     */
    protected $backgroundHelper;
    protected $pageWithBackgrounds;

    /** @var  Collection */
    protected $backgroundFiles;
    protected $backgroundOrder;

    public function __construct() {
        parent::__construct();

        $helperClass = $GLOBALS['FBI']['helperClass'];

        if (class_exists($helperClass)) {
            $this->backgroundHelper = new $helperClass();
        } else {
            $this->backgroundHelper = new Helper();
        }
    }

    public function generate(\PageModel $page, \LayoutModel $layout, \PageRegular $pageRegular) {
        // skip in backend view
        if (TL_MODE == 'BE') {
            return;
        }

        $this->pageWithBackgrounds = $this->backgroundHelper->findAll($page);

        // Return if there are no files
        if (!$this->pageWithBackgrounds) {
            return;
        }

        // Get the file entries from the database
        $this->backgroundFiles = \FilesModel::findMultipleByUuids($this->pageWithBackgrounds->fbiSRC);

        if ($this->backgroundFiles === null) {
            if (!\Validator::isUuid($this->pageWithBackgrounds->fbiSRC[0])) {
                \System::log($GLOBALS['TL_LANG']['ERR']['version2format'], __METHOD__, TL_ERROR);
            }

            return;
        }

        $this->fbiTemplate = $this->pageWithBackgrounds->fbiTemplate;
        $this->caption = (int)$this->pageWithBackgrounds->fbiImgCaption;
        $this->mode = $this->pageWithBackgrounds->fbiImgMode;
        $this->sortBy = $this->pageWithBackgrounds->sortBy;
        $this->speed = $this->pageWithBackgrounds->fbiSpeed;
        $this->timeout = $this->pageWithBackgrounds->fbiTimeout;
        $this->order = $this->pageWithBackgrounds->fbiOrder;
        $this->fbiLimit = (int)$this->pageWithBackgrounds->fbiLimit;
        $this->nav = (int)$this->pageWithBackgrounds->fbiEnableNav;
        $this->navClick = (int)$this->pageWithBackgrounds->fbiNavClick;
        $this->navPrevNext = (int)$this->pageWithBackgrounds->fbiNavPrevNext;
        $this->centerX = (int)$this->pageWithBackgrounds->fbiCenterX;
        $this->centerY = (int)$this->pageWithBackgrounds->fbiCenterY;

        $this->compile($page);
    }

    public function compile(\PageModel $page) {
        $images = array();
        $auxDate = array();

        $backgrounds = $this->backgroundFiles;

        while ($backgrounds->next()) {
            // Continue if the files has been processed or does not exist
            if (isset($images[$backgrounds->path]) || !file_exists(TL_ROOT . '/' . $backgrounds->path)) {
                continue;
            }

            // Single files
            if ($backgrounds->type == 'file') {
                $file = new \File($backgrounds->path, true);

                if (!$file->isImage) {
                    continue;
                }

                $arrMeta = $this->getMetaData($backgrounds->meta, $page->language);

                if (empty($arrMeta)) {
                    if ($this->metaIgnore) {
                        continue;
                    } elseif ($page->rootFallbackLanguage !== null) {
                        $arrMeta = $this->getMetaData($backgrounds->meta, $page->rootFallbackLanguage);
                    }
                }

                // Use the file name as title if none is given
                if ($arrMeta['title'] == '') {
                    $arrMeta['title'] = specialchars(str_replace('_', ' ', $file->filename));
                }

                // Add the image
                $images[$file->path] = [
                    'id' => $backgrounds->id,
                    'uuid' => $backgrounds->uuid,
                    'name' => $file->basename,
                    'singleSRC' => $file->path,
                    'alt' => ($arrMeta['caption'] ?: $arrMeta['title']),
                    'title' => $arrMeta['title'],
                    'imageUrl' => $arrMeta['link'],
                    'caption' => $arrMeta['caption'],
                ];

                $auxDate[] = $file->mtime;
            } else { // Folders
                $subfiles = \FilesModel::findByPid($backgrounds->uuid);

                if ($subfiles === null) {
                    continue;
                }

                while (null !== $subfiles && $subfiles->next()) {

                    // Skip subfolders
                    if ($subfiles->type == 'folder') {
                        continue;
                    }

                    $file = new \File($subfiles->path, true);

                    if (!$file->isGdImage) {
                        continue;
                    }

                    $arrMeta = $this->getMetaData($subfiles->meta, $page->language);

                    // Use the file name as title if none is given
                    if ($arrMeta['title'] == '') {
                        $arrMeta['title'] = specialchars(str_replace('_', ' ', $file->filename));
                    }

                    // Add the image
                    $images[$file->path] = [
                        'id' => $subfiles->id,
                        'uuid' => $subfiles->uuid,
                        'name' => $file->basename,
                        'singleSRC' => $file->path,
                        'alt' => ($arrMeta['caption'] ?: $arrMeta['title']),
                        'title' => $arrMeta['title'],
                        'imageUrl' => $arrMeta['link'],
                        'caption' => $arrMeta['caption'],
                    ];

                    $auxDate[] = $file->mtime;
                }
            }

            // Sort array
            switch ($this->sortBy) {
                default:
                case 'name_asc':
                    uksort($images, 'basename_natcasecmp');
                    break;

                case 'name_desc':
                    uksort($images, 'basename_natcasercmp');
                    break;

                case 'date_asc':
                    array_multisort($images, SORT_NUMERIC, $auxDate, SORT_ASC);
                    break;

                case 'date_desc':
                    array_multisort($images, SORT_NUMERIC, $auxDate, SORT_DESC);
                    break;

                case 'meta': // Backwards compatibility
                case 'custom':
                    if ($this->order != '') {
                        $tmp = deserialize($this->order);

                        if (!empty($tmp) && is_array($tmp)) {
                            // Remove all values
                            $order = array_map(function () {
                            }, array_flip($tmp));

                            // Move the matching elements to their position in $arrOrder
                            foreach ($images as $k => $v) {
                                if (array_key_exists($v['uuid'], $order)) {
                                    $order[$v['uuid']] = $v;
                                    unset($images[$k]);
                                }
                            }

                            // Append the left-over images at the end
                            if (!empty($images)) {
                                $order = array_merge($order, array_values($images));
                            }

                            // Remove empty (unreplaced) entries
                            $images = array_values(array_filter($order));
                            unset($order);
                        }
                    }
                    break;

                case 'random':
                    shuffle($images);
                    break;
            }
        }

        // Limited images
        if ($this->fbiLimit && 0 != $this->fbiLimit && count($images) > $this->fbiLimit) {
            $images = array_slice($images, 0, $this->fbiLimit);
        }

        $images = array_values($images);
        $maxWidth = $GLOBALS['TL_CONFIG']['maxImageWidth'];
        $imageObjects = [];
        $imageIndex = 0;

        if (count($images)) {
            if ($this->mode === 'random') {
                mt_srand();
                $imageIndex = mt_rand(0, count($images) - 1);
            }

            foreach ($images as $image) {
                $objCell = new \stdClass();
                $this->addImageToTemplate($objCell, $image, $maxWidth);
                $imageObjects[] = $objCell;
            }

            if ($this->mode === 'single' || $this->mode === 'random') {
                $imageObjects = [
                    $imageObjects[$imageIndex]
                ];
            }

            $template = 'fbi_default';

            if ($this->fbiTemplate != '' && TL_MODE == 'FE') {
                $template = $this->fbiTemplate;
            }

            $templateObject = new \FrontendTemplate($template);

            $templateObject->images = implode(
                ',',
                array_map(function ($image) {
                    $imgObj = new \stdClass();
                    $imgObj->src = $image->src;
                    $imgObj->alt = $image->alt;
                    $imgObj->title = $image->title;
                    $imgObj->caption = $image->caption;
                    return json_encode($imgObj);
                }, $imageObjects)
            );

            $templateObject->timeout = (int)$this->timeout ? $this->timeout : 12000;
            $templateObject->speed = (int)$this->speed ? $this->speed : 1200;
            $templateObject->caption = $this->caption ? 'true' : 'false';
            $templateObject->nav = $this->nav ? 'true' : 'false';
            $templateObject->navClick = $this->navClick ? 'true' : 'false';
            $templateObject->navPrevNext = $this->navPrevNext ? 'true' : 'false';
            $templateObject->centerX = $this->centerY ? 'true' : 'false';
            $templateObject->centerY = $this->centerY ? 'true' : 'false';

            // add javascript and css files
            $GLOBALS['TL_CSS'][] = 'bundles/contaofullbackgroundimages/css/style.css||static';
            $GLOBALS['TL_BODY'][] = $templateObject->parse();
            $GLOBALS['TL_BODY'][] = \Contao\Template::generateScriptTag('bundles/contaofullbackgroundimages/js/eventListener.polyfill.js', false, null);
            $GLOBALS['TL_BODY'][] = \Contao\Template::generateScriptTag('bundles/contaofullbackgroundimages/js/jquery.backstretch.min.js', false, null);
            $GLOBALS['TL_BODY'][] = \Contao\Template::generateScriptTag('bundles/contaofullbackgroundimages/js/fullbackground.js', false, null);
        }
    }
}
