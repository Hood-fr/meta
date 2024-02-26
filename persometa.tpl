{if isset ($metapersos)}
  {foreach from=$metapersos item=metaperso}
  <meta {$metaperso.METATYPE}="{$metaperso.METANAME}" content="{$metaperso.METAVAL}">
  {/foreach}
{/if}