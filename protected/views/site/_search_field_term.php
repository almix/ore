<div class="<?php echo $divClass; ?>">
	<span class="search-term<?php echo $isInner ? ' search-term-inner' : ''?>">
		<input type="text" onblur="if (this.value == '') {this.value = <?php echo CJavaScript::encode(tc("Search by description or address"));?>;}" onfocus="if (this.value == <?php echo CJavaScript::encode(tc("Search by description or address"));?> ) { this.value = ''; }" value="<?php echo (isset($this->term)) ? $this->term : tc("Search by description or address");?>" class="textbox" name="term" id="search_term_text" maxlength="50">
		<input type="submit" value="<?php echo tc("Search");?>" onclick="prepareSearch(); return false;">
		<input type="hidden" value="0" id="do-term-search" name="do-term-search">
	</span>
</div>