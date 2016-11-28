(function($){
    "use strict";

    function Blog() {
        var $this = this;
        this.postContainer = $('.blog-list-wrapper > .container > .row');
        this.perPage = LpData.perPage;
        this.totalPosts = LpData.totalPost;
        this.loader = $('.loader');
        this.lastItem = function() {
            return $('.blog-item').last();
        };
        this.tag = LpData.tag;
        this.didScroll = false;
        this.onPage =  $('.blog-item').length;
        this.renderHtml = function(postdata) {
            var postHtml = '';
            $.each(postdata, function(idx, val) {
                postHtml += '<article id="post-' + val.id + '" class="blog-item" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">' +
                    '<div class="blog-inner-wrapper">' +
                    '<div class="blog-thumbnail" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">' +
                    '<meta itemprop="contentUrl" content="' + val.image + '">' +
                    '<a href="' + val.link + '" class="blog-thumbnail-holder open-post-modal" data-id="' + val.id + '">' +
                    '<img src="' + val.image + '" alt="' + val.title + '" class="img-responsive" itemprop="contentUrl">' +
                    '</a></div>' +
                    '<div class="blog-info-holder">' +
                    '<h2 class="info-title" itemprop="headline"><a href="' + val.link + '" class="open-post-modal" data-id="' + val.id + '">' + val.title + '</a></h2>' +
                    '<div class="entry-meta">' +
                    '<time class="updated" datetime="' + val.dates.format + '">' + val.dates.view + '</time>' +
                    val.tag + '</div>' +
                    '</div></div></article><!-- /.blog-item -->';
            });
            $this.postContainer.append(postHtml);
        };
        this.getPosts = function() {
            $this.loader.show();
            var data = {
                'action' : 'do_ajax',
                'fn' : 'get_blog_posts',
                'offset' : $this.onPage,
                'posts_per_page': $this.perPage
            };
            if( $this.tag ) {
                data.tag = $this.tag;
            }
            $.when(
                $.ajax({
                    url: LpData.ajaxUrl,
                    dataType : 'json',
                    method: 'post',
                    data: data,
                    success : function(data){
                        if(data.length !== 'undefined' && data.length > 0 ){
                            $this.onPage += data.length;
                            $this.renderHtml(data);
                        } else {

                        }

                    },
                    error : function (error){
                        console.error(error);
                    },
                    complete: function() {
                        $this.loader.hide();
                    }
                })
            ).then(function(){
                    if(  $this.onPage < $this.totalPosts ) {
                        if(window.lpw.Helpers.isElementIntoView($this.lastItem())) {
                            $this.getPosts();
                        }
                        $this.didScroll = false;
                    } else {
                        $(window).off('scroll.lprop', $this.getPosts);
                        $(window).off('load.lprop', $this.getPosts);
                        $(window).off('resize.lprop', $this.getPosts);
                    }
                });
        };
        this.scrollPage = function() {
            if (window.lpw.Helpers.isElementIntoView($this.lastItem()) && !$this.didScroll) {
                $this.didScroll = true;
                $this.getPosts();
            }
        };
        this.eventListeners = function() {
            if( this.onPage < this.totalPosts) {
                $(window).on('scroll.lprop', $this.scrollPage);

                if (window.lpw.Helpers.isElementIntoView($this.lastItem())) {
                    $(window).on('load.lprop', $this.getPosts);
                    $(window).on('resize.lprop', $this.getPosts);
                }
            }
        };
        this.init = function() {
            this.eventListeners();
        };
    }

    window.lpw = window.lpw || {};
    window.lpw.Blog = Blog;
})(jQuery);