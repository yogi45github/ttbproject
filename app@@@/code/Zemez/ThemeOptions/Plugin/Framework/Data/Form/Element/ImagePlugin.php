<?php

namespace Zemez\ThemeOptions\Plugin\Framework\Data\Form\Element;

use \Magento\Framework\Data\Form\Element\Image;
use Magento\Framework\View\Helper\Js as JsHelper;

/**
 * Form Image plugin.
 *
 * @package Zemez\ThemeOptions\Plugin\Framework\Data\Form\Element
 */
class ImagePlugin
{

    /**
     * @var JsHelper
     */
    protected $jsHelper;

    /**
     * @var logoId
     */
    protected $_logoId;

    /**
     * @var faviconId
     */
    protected $_faviconId;


    /**
     * ImagePlugin constructor.
     *
     * @param JsHelper $jsHelper
     */
    public function __construct(JsHelper $jsHelper)
    {
        $this->jsHelper = $jsHelper;
        $this->_logoId = 'theme_options_general_logo_settings_logo_image';
        $this->_faviconId = 'theme_options_general_seo_settings_favicon';
    }

    /**
     * Change image preview
     *
     * @return string
     *
     */
    public function afterGetElementHtml(Image $subject, $result)
    {
        $subjectId = $subject->getId();
        return ($subjectId == $this->_logoId || $subjectId == $this->_faviconId) ? $result . $this->_getExtraJs($subject) : $result;
    }

    /**
     * Get extra JS
     *
     * @return string
     */
    protected function _getExtraJs(Image $subject)
    {
        $output = <<<EOL
            require(['jquery'], function($) {
                $(function() {
                    var selector = $('#{$this->_logoId}, #{$this->_faviconId}');
                    $.each(selector, function(){
                        $(this).on('change', function(){
                            var _this = $(this);
                            if (this.files && this.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function (e) {
                                    var id = _this.attr('id');
                                    var preview = $('#' + id + '_image');
                                    if(preview.length) {
                                        $(preview)
                                            .attr('src', e.target.result)
                                            .width(200)
                                            .height('auto');
                                    } else {
                                        var newPreview = document.createElement('img');
                                        var newPreviewLink = document.createElement('a');
                                        $(newPreviewLink).attr('id', 'temp-link');
                                        $(newPreview)
                                            .attr({
                                                'src': e.target.result,
                                                'id': id + '_image' 
                                            })
                                            .width(200)
                                            .height('auto');
                                        $(newPreviewLink).prepend(newPreview);
                                        $('tr#row_'+id + ' td.value').prepend(newPreviewLink);
                                    }
                                };
                                reader.readAsDataURL(this.files[0]);
                            }
                        });  
                    })
                });
            });
EOL;

        return $this->jsHelper->getScript($output);
    }


}