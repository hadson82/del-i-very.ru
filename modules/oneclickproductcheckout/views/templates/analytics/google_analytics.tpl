<script>
    {literal}
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    {/literal}
    ga('create', '{$key}', 'auto');
    ga('require', 'ec');
    {foreach from=$ga_data item=product}
    ga('ec:addProduct', {literal}{{/literal}
        {foreach from=$product key=param item=value}
        {if $value}
        '{$param}':'{$value}',
        {/if}
        {/foreach}
        {literal}}{/literal});
    ga('ec:setAction', 'purchase', {literal}{{/literal}
        {foreach from=$product key=param item=value}
        {if $value}
        '{$param}':'{$value}',
        {/if}
        {/foreach}
        {literal}}{/literal});
    ga('send', 'pageview');
    {/foreach}
</script>
