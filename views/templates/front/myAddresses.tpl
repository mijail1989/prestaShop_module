{extends file="page.tpl"} {block name='page_content'} 
<h2 class="m-2 custom-text-center">Your Personal Addresses</h2>
{foreach from=$apiData item=item}
<div class="col-md-4">
    <div class="card-body border-radius border-custom">
      <h3 class="custom-text-center  mb-2">{$item.name}</h3>
      {foreach from=$item item=value key=key}
      <p class="black"> <span class="custom-key-color">{$key}</span>: {$value}</p>
      {/foreach}
    </div>
  </div>
{/foreach}
{/block}
