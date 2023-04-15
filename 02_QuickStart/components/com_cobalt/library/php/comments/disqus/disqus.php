<?php
include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_cobalt/library/php/cobaltcomments.php';

class CobaltCommentsDisqus extends CobaltComments {

	public function getNum($type, $item)
	{
		static $load = null;
		if(!$type->params->get('comments.shortname'))
		{
			return 'No Disqus short name';
		}
		if(!$load)
		{
			$js = "var disqus_shortname = '".$type->params->get('comments.shortname')."';
			jQuery(document).ready(function(){
				var s = document.createElement('script');
				s.type = 'text/javascript';
				s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
				(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
			});";

			JFactory::getDocument()->addScriptDeclaration($js);


			$load = TRUE;
		}

		return "<a data-disqus-identifier=\"".$type->params->get('comments.ident').$item->id."\" href=\"".JRoute::_($item->url)."#disqus_thread\">".JText::_('Counting...')."</a>";
	}

	public function getComments($type, $item)
	{
		if(!$type->params->get('comments.shortname'))
		{
			return 'No Disqus short name';
		}
		$section = ItemsStore::getSection($item->section_id);
		$out = '
		<h2>'.JText::_('CCOMMENTS').'</h2>
		<div id="disqus_thread"></div>
		<script type="text/javascript">
		var disqus_shortname = \''.$type->params->get('comments.shortname').'\';
		var disqus_url = \''.$item->href.'\';
		var disqus_identifier = \''.$type->params->get('comments.ident').$item->id.'\';
		//var disqus_developer = 1;
		function disqus_config() {
			this.callbacks.onNewComment = [function(comment) { trackComment(comment, '.$item->id.'); }];
		}
		/* * * DON\'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
        dsq.src = \'http://\' + disqus_shortname + \'.disqus.com/embed.js\';
        (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
    })();
		</script>
		<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
		<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>';

		return $out;
	}
}