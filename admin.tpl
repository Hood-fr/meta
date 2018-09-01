{footer_script}
jQuery(document).ready(function(){
  jQuery('.categoryLi').mouseover(function(){
    jQuery(this).children('a').show();
  });
  jQuery('.categoryLi').mouseout(function(){
    jQuery(this).children('a').hide();
  });
});
{/footer_script}

<div class="titrePage">
  <h2>meta</h2>
</div>
{if isset ($gestionA)}
  <h3>{'meta_desh3'|@translate}</h3>
	<div>
	  <form method="post" >
		<fieldset>
		  <legend>{'meta_select'|@translate}</legend>
		  {'meta_list'|@translate}	
		  {html_options name="metalist" options=$gestionA.OPTIONS selected=$gestionA.SELECTED}
		  <br>	
		  <br>
		  <div style="text-align:center;">
		    <input class="submit" name="submitchoixmeta" type="submit" value="{'meta_choix'|@translate}"/>
		  </div>
		</fieldset>
	  </form>
	</div>
  {if isset ($meta_edit)}
	<div>
	<form method="post" >
	  <fieldset>
		<legend>{'meta_comp'|@translate}</legend>
		<input type="hidden" name="invisible" value="{$meta_edit.VALUE}">
		{$meta_edit.VALUE}&nbsp;&nbsp;<input type="text" name="inser" value="{$meta_edit.CONTENT}" size="100" maxlenght="100">
		<br>	
		<br>
		<div style="text-align:center;">
		  <input class="submit" name="submitinsmeta" type="submit" value="{'meta_insmeta'|@translate}" {$TAG_INPUT_ENABLED} />
		</div>
	  </fieldset>
	</form>
	</div>
  {/if}
{/if}

{if isset ($description)}
  <div class="comment">
	<h3>{'meta_author'|@translate}</h3>
		{'meta_author_help'|@translate}
  </div>
  <div class="comment">
	<h3>{'meta_keywords'|@translate}</h3>
		{'meta_keywords_help'|@translate}
  </div>
  <div class="comment">
	<h3>{'meta_Description'|@translate}</h3>
		{'meta_Description_help'|@translate}
  </div>
  <div class="comment">
	<h3>{'meta_robots'|@translate}</h3>
		{'meta_robots_help'|@translate}
  </div>
{/if}
{if isset ($metapersoT)}
  <form method="post" >
	<fieldset>
	  <input class="submit" name="submitaddpersonalmeta" type="submit" value="{'Add Personal metadata'|@translate}" {$TAG_INPUT_ENABLED} />
	</fieldset>
  </form>
  <form method="post" >
	<fieldset>
	  <legend>{'List Personal Metadata'|@translate}</legend>
	  <ul class="categoryUl">
		{foreach from=$metapersos item=metaperso}
		  <li class="categoryLi virtual_cat">
			< meta {$metaperso.METATYPE}="{$metaperso.METANAME}" content="{$metaperso.METAVAL}">
			<a href="{$metaperso.U_EDIT}" style="display: none"><span class="icon-pencil"></span>{'Edit'|@translate}</a>
			<a href="{$metaperso.U_DELETE}" style="display: none" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');"><span class="icon-trash"></span>| {'delete'|@translate}</a>
			<br>
		  </li>
		{/foreach}
	  </ul>
	</fieldset>
  </form>
  {if isset ($meta_edit2)}
	<form method="post" >
	  <fieldset>
		<legend>{'Personnal metadata'|@translate}</legend>
		<input type="hidden" name="invisibleID" value="{$meta_edit2.METAID}">
		< meta <input type="text" name="insertype" value="{$meta_edit2.METATYPE}" size="30" maxlenght="30"> ="<input type="text" name="insername" value="{$meta_edit2.METANAME}" size="30" maxlenght="30">" content="<input type="text" name="inserval" value="{$meta_edit2.METAVAL}" size="60" maxlenght="100">">
		<br>	
		<br>
			<div style="text-align:center;">
			<input class="submit" name="submitaddmetaperso" type="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED} />
			</div>
	  </fieldset>
	</form>
  {/if}
{/if}

{if isset ($contactmetaT)}
  <form method="post" >
    <fieldset id="mainConf">
	  {'Keywords of contact page to be completed'|@translate}&nbsp;:<br>
	  <input type="text" name="inser" value="{$contactmetaT.CMKEY}" size="110" maxlenght="110">
	  <br>	
	  <br>
	  {'Description of contact page to be completed'|@translate}&nbsp;:<br>
	  <input type="text" name="inser2" value="{$contactmetaT.CMDESC}" size="110" maxlenght="110">
	  <br>	
	  <br>
	  <p>
		<input class="submit" type="submit" name="submitcm" value="{'Submit'|@translate}">
		<input class="submit" type="reset" name="reset" value="{'Reset'|@translate}">
	  </p>
  </form>
{/if}

{if isset ($gestionC)}
  <div>
	<form method="post" >
	  <fieldset>
		<legend>{'Choose Additional Pages'|@translate}</legend>
		{html_options name="APchoix" options=$gestionC.OPTIONS selected=$gestionC.SELECTED}
		<br>	
		<br>
		  <div style="text-align:left;">
			<input class="submit" name="submitchoixAP" type="submit" value="{'Submit'|@translate}" />
		  </div>
	  </fieldset>
	</form>
  </div>
  {if isset ($ap_edit)}
	<div>
	  <form method="post" >
		<fieldset>
		  <legend>{'Add metadata for page'|@translate} {$ap_edit.VALUEN} (id : {$ap_edit.VALUE})</legend>
		  <input type="hidden" name="invisible" value="{$ap_edit.VALUE}">
		  <br>
		  {'Keywords of Additional Pages to be completed'|@translate}<br>
		  <input type="text" name="inser" value="{$ap_edit.CONTENTMKAP}" size="110" maxlenght="110">
		  <br>	
		  <br>
		  {'Description of Additional Pages to be completed'|@translate}<br>
		  <input type="text" name="inser2" value="{$ap_edit.CONTENTMDAP}" size="110" maxlenght="110">
		  <br>	
		  <br>
		  <div style="text-align:left;">
			<input class="submit" name="submitinsapm" type="submit" value="{'Submit'|@translate}"/>
		  </div>
		</fieldset>
	  </form>
	</div>
  {/if}
{/if}