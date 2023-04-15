<?php
/**
 * BranchSelector module for Joomla
 * @encoding     UTF-8
 * @version      1.0
 * @copyright    Copyright (C) 2015 Torbara (http://alexander.khmelnitskiy.ua). All rights reserved.
 * @license      GNU General Public License version 2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 * @author       Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 */

defined('_JEXEC') or die;


$core = array('type_id' => 'Type', 'user_id','','','','','','','','', );

$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');
?>

<div id="mod_cobalt_ajaxrecords_<?php echo $module->id; ?>" class="mod_cobalt_ajaxrecords <?php echo $moduleclass_sfx; ?>">
    <div class="mod_cobalt_ajaxrecords_box">
        <div class="uk-container uk-container-center">
            
            <div class="uk-grid">
                <?php if($isroot ) : ?>
                    <div class="navbar" id="cnav">
                        <div class="navbar-inner">
                            <ul class="nav">
                                <li class="dropdown">
                                    <a href="/add-portfolio">
                                    <i class="icon-plus"></i>																		Post Gallery Album here								</a>
                                </li>

                            </ul>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="portfolio-list uk-width-medium-8-10 uk-push-1-10">
                    <?php echo $mod_result; ?>
                    
                    <div class="portfolio-list-item port-load-wrap">
                        <div class="its-btn"><a href="#" class="ak-load-more-btn" data-ajax-step="<?php echo $step; ?>"><?php echo JText::_('Load more'); ?></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script type="text/javascript">
    jQuery(function($) {
        var uid = "#mod_cobalt_ajaxrecords_<?php echo $module->id; ?>";
        
        jQuery(uid + " .ak-load-more-btn").click(function (e) { //Load more button
            e.preventDefault();
            
            var step = jQuery(this).data("ajax-step");
            
            step = step + 1; //next step
            jQuery.get("<?php echo $ajax_url; ?>" + step, function(data){
                if(!data.length){
                    jQuery(uid + " .ak-load-more-btn").remove();
                }else{
                    jQuery(data).addClass('uk-animation-slide-top').insertBefore(uid + " .mod_cobalt_ajaxrecords_box .portfolio-list > *:last-child");
                    
                }
            });
            jQuery(this).data("ajax-step", step);
        });
        
    });
</script>