(function($){
    "use strict";

    function Single() {
        var $this = this;
        this.postContainer = $('.blog-list-wrapper');
        this.isModalExists = function() {
            return $('.single-post-modal').length;
        };
        this.closeModal = function(ev) {
            ev.preventDefault();
            $('.single-post-modal').remove();
            $('html').removeClass('overflow-height');
            if( window.lpw.Helpers.isHhistoryApiAvailable()) {
                window.history.pushState(null, null, $this.location);
            }
        };
        this.renderHtml = function(postData) {
            $('html').addClass('overflow-height');

            if( $this.isModalExists() === 0 ) {
                var postHtml = '<div class="single-post-container single-post-modal">' +
                    '<header class="single-post-header">' +
                    '<div class="single-post-wrap">' +
                    '<button type="button" class="btn btn-single-close"><span>Close</span></button>' +
                    '<div class="social-sharing"><ul>' +
                    '<li class="label">Share</li>' +
                    '<li><a href="mailto:?subject=' + postData.post.title + '&body=' + postData.post.link + '" class="soc-icon email-icon"></a></li>' +
                    '<li><a href="https://www.facebook.com/sharer/sharer.php?u=' + postData.post.url + '" target="_blank" class="soc-icon fb-icon"></a></li>' +
                    '<li><a href="https://twitter.com/intent/tweet?text=' + postData.post.share_title + '&url=' + postData.post.url + '&via=leadingpro" target="_blank" class="soc-icon twitter-icon"></a></li>' +
                    '<li><a href="https://www.linkedin.com/shareArticle?mini=true&url=' + postData.post.url + '&title=' + postData.post.share_title + '&summary=' + postData.post.excerpt + '" target="_blank" class="soc-icon ln-icon"></a></li>' +
                    '<li><a href="https://plus.google.com/share?url=' + postData.post.url + '" target="_blank" class="soc-icon gplus-icon"></a></li>' +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '</header><!-- /.single-post-header -->' +
                    '<div class="single-post-content">' +
                    '<div class="single-post-content-inner">' +
                    '<div class="single-post-thumbnail">' +
                    '<img src="' + postData.post.image + '" alt="' + postData.post.title + '" class="img-responsive">' +
                    '</div>' +
                    '<div class="single-post-details"><h1 class="single-post-title" itemprop="headline">' + postData.post.title + '</h1>' +
                    '<div class="entry-meta main-entry-meta">' +
                    '<time class="updated" datetime="' + postData.post.dates.format + '">' + postData.post.dates.view + '</time>' +
                    '<span class="post-tags">' + postData.post.tag + '</span></div>' +
                    '<div class="entry-content" itemprop="articleBody">' + postData.post.content + '</div><!-- /.entry-content -->' +
                    '</div><!-- /.single-post-details --></div><!-- /.single-post-content-inner --></div><!-- /.single-post-content -->';
                if(postData.adj.length > 0 ) {
                    postHtml += '<div class="adjacent-posts-container blog-list-wrapper"><div class="container"><div class="row">';
                    $.each(postData.adj, function(idx, val) {
                        postHtml += '<article id="post-' + val.id + '" class="blog-item adjacent-item" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">' +
                            '<div class="blog-inner-wrapper">' +
                            '<div class="blog-thumbnail" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">' +
                            '<a href="' + val.link + '" class="blog-thumbnail-holder open-post-modal" data-id="' + val.id + '">' +
                            '<img src="' + val.image + '" alt="' + val.title + '" class="img-responsive">' +
                            '</a></div>' +
                            '<div class="blog-info-holder">' +
                            '<h2 class="info-title" itemprop="headline"><a href="' + val.link + '">' + val.title + '</a></h2>' +
                            '<div class="entry-meta">' +
                            '<time class="updated" datetime="' + val.dates.format + '">' + val.dates.view + '</time>' +
                            '<span class="post-tags">' + val.tag + '</span></div>' +
                            '</div></div></article><!-- /.blog-item -->';
                    });
                    postHtml += '</div></div></div>';
                }
                postHtml += '<div class="single-object-backdrop"></div></article><!-- /.single-post-container -->';

                $this.postContainer.append(postHtml);
            } else {
                $('.single-post-modal .email-icon').attr("href", 'mailto:?subject=' + postData.post.title + '&body=' + postData.post.link);
                $('.single-post-modal .fb-icon').attr("href", 'https://www.facebook.com/sharer/sharer.php?u=' + postData.post.url);
                $('.single-post-modal .twitter-icon').attr("href", 'https://twitter.com/intent/tweet?text=' + postData.post.share_title + '&url=' + postData.post.url + '&via=leadingpro');
                $('.single-post-modal .ln-icon').attr("href", 'https://www.linkedin.com/shareArticle?mini=true&url=' + postData.post.url + '&title=' + postData.post.share_title + '&summary=' + postData.post.excerpt);
                $('.single-post-modal .gplus-icon').attr("href", 'https://plus.google.com/share?url=' + postData.post.url);
                $('.single-post-thumbnail img').attr("src", postData.post.image)
                    .attr("alt", postData.post.title);
                $('.single-post-thumbnail a').attr("src", postData.post.link);
                $('.single-post-title').text(postData.post.title);
                $('.main-entry-meta .updated').attr("datetime", postData.post.dates.format).text(postData.post.dates.view);
                $('.main-entry-meta .post-tags').html(postData.post.tag);
                $('entry-content').html(postData.post.content);
                $('.adjacent-item').each(function(idx) {
                    if(postData.adj[idx] !== 'undefined') {
                        $(this).attr("id", 'post-' + postData.adj[idx].id)
                            .find('.blog-thumbnail-holder')
                            .attr("href", postData.adj[idx].link)
                            .data("id", postData.adj[idx].id)
                            .find("img")
                            .attr("src", postData.adj[idx].image)
                            .attr("alt", postData.adj[idx].title);
                        $(this).find('.info-title a')
                            .attr("href", postData.adj[idx].link)
                            .data("id", postData.adj[idx].id)
                            .text(postData.adj[idx].title);
                        $(this).find('.updated').attr("datetime", postData.adj[idx].dates.format).text(postData.adj[idx].dates.view);
                        $(this).find('.post-tags').html(postData.adj[idx].tag);

                    }
                });
            }
        };
        this.getSinglePost = function(ev) {
            ev.preventDefault();
            var data = {
                'action' : 'do_ajax',
                'fn' : 'get_single_post'
            };
            $this.showLoader(true);
            if(ev.type === 'click') {
                var url = $(this).attr('href');
                data.type = 'id';
                data.id = $(this).data('id');
            } else {
                data.type = 'slug';
                data.id = window.location.pathname;
            }
            $.ajax({
                url: LpData.ajaxUrl,
                dataType : 'json',
                method: 'post',
                data : data,
                success : function(data){
                    if(typeof data === 'object' ){
                        $this.renderHtml(data);
                        if(url !== $this.location){
                            if( window.lpw.Helpers.isHhistoryApiAvailable() && 'click' === ev.type) {
                                window.history.pushState(null, null, url);
                            }
                        }
                    } else {}
                },
                error : function (error){
                    console.error(error);
                },
                complete: function() {
                    $this.showLoader(false);
                }
            });

        };
        this.testPopstste = function(ev) {
            if( $this.isModalExists && window.location.pathname === $this.location) {
                $('.btn-single-close').trigger('click.lprop');
            } else if(window.location.pathname ) {
                $this.getSinglePost(ev);
            }
        };
        this.showLoader = function(state) {
            if(state) {
                $('<div class="post-overlay loader"><span class="spin"></span></div>').appendTo($this.postContainer);
            } else {
                $('.post-overlay').remove();
            }
        };
        this.eventListeners = function() {
            this.postContainer.on('click.lprop', '.open-post-modal', $this.getSinglePost);
            $(window).on('popstate', $this.testPopstste);
            this.postContainer.on('click.lprop', '.btn-single-close', $this.closeModal);
            this.postContainer.on('click.lprop', '.single-object-backdrop', $this.closeModal);
        };
        this.init = function() {
            this.location = window.location.pathname;
            this.eventListeners();
        };
    }

    window.lpw = window.lpw || {};
    window.lpw.Single = Single;
})(jQuery);