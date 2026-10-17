<script>
  (function(d,t) {
    var BASE_URL="http://fidentta-chatwoot-d218d0-187-7-73-209.sslip.io";
    var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
    g.src=BASE_URL+"/packs/js/sdk.js";
    g.async = true;
    s.parentNode.insertBefore(g,s);
    g.onload=function(){
      window.chatwootSDK.run({
        websiteToken: 'pBrNdA7LpK3ysYdq9ZCZWSxT',
        baseUrl: BASE_URL
      })
    }
  })(document,"script");
</script>
