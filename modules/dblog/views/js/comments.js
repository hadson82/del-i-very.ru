/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Goryachev Dmitry    <dariusakafest@gmail.com>
 * @copyright 2007-2016 Goryachev Dmitry
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

Comment = {
    current_ajax: null,
    _interval_update: null,
    init: function () {
        var _this = this;
        $.fn.setCenPosAbsBlock = function ()
        {
            var offsetElemTop = 20;
            var scrollTop = $(document).scrollTop();
            var elemWidth = $(this).width();
            var windowWidth = $(window).width();
            $(this).css({
                top: ($(this).height() > $(window).height() ? scrollTop + offsetElemTop : scrollTop + (($(window).height()-$(this).height())/2)),
                left: ((windowWidth-elemWidth)/2)
            });
        };

        $.fn.moveToEnd = function ()
        {
            var target = $(this).get(0);
            var rng, sel;
            if ( document.createRange ) {
                rng = document.createRange();
                rng.selectNodeContents(target);
                rng.collapse(false); // схлопываем в конечную точку
                sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange( rng );
            } else { // для IE нужно использовать TextRange
                var rng = document.body.createTextRange();
                rng.moveToElementText( target );
                rng.collapseToEnd();
                rng.select();
            }
        };

        $('body').prepend($('#template_popups_comment').html());
        $('#template_popups_comment').remove();
        
        $(window).resize(function () {
            $('.comment_popup').setCenPosAbsBlock();
        });

        $('.comment_stage_popup, .comment_popup_close').live('click', function (e) {
            e.preventDefault();
            $('.comment_popup_login, .comment_stage_popup').stop(true, true).fadeOut(300);
        });


        $('.upload_avatar').live('change', function () {
            var self = $(this);
            var data = new FormData();
            data.append('ajax', true);
            data.append('action', 'upload_avatar');
            data.append('avatar', $(this).get(0).files[0]);
            $.ajax({
                url: api_path,
                type: 'POST',
                dataType: 'json',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(r)
                {
                    if (r.hasError)
                        Comment.log.error(r.errors.join('\n'));
                    else
                        _this.setAvatar(r.avatar);
                }
            });
        });

        $('.comment_login').live('click', function (e) {
            e.preventDefault();
            $(window).resize();
            $('.comment_popup_login, .comment_stage_popup').stop(true, true).fadeIn(300);
        });
        
        $('.click_login').live('click', function () {
            var form = $('.comment_popup_login');
            var form_data = form.find(':input').serialize();
            form.find('.popup_body').addClass('_loading');
            form.find('.popup_error').remove();
            $.ajax({
                url: api_path,
                type: 'POST',
                dataType: 'json',
                data: form_data + '&ajax=true&action=login',
                success: function (r) {
                    form.find('.popup_body').removeClass('_loading');
                    if (r.hasError)
                    {
                        form.find('.popup_body').prepend('<div class="popup_error">'+r.errors.join('<br>')+'</div>');
                    }
                    else
                    {
                        comment_conf.logged = true;
                        logged = true;
                        $('[data-create-account]').remove();
                        form.find('.popup_body').prepend('<div class="popup_success">'+r.success+'</div>');
                        setTimeout(function () {
                            $('.comment_popup, .comment_stage_popup').remove();
                        }, 2000);

                        if (r.path_avatar)
                            _this.setAvatar(r.path_avatar);
                    }
                },
                error: function () {
                    form.find('.popup_body').removeClass('_loading');
                }
            });
        });

        this.activateIntervalUpdate();

        $('.comment_detail').live('mouseover',function () {
            $(this).removeClass('no_viewed')
        });
        $('[data-form-comment] [name="message"]').live('keyup',function (event) {
            if(event.keyCode == 13)
            {
                event.preventDefault();
                $(this).closest('[data-form-comment]').find('.btn_add').trigger('click');
            }
        });
        $('.view_more').live('click', function (e) {
            e.preventDefault();
            var self = $(this);
            self.addClass('_loading_view_more');

            var parent_id = (self.data('parent-id') ? self.data('parent-id') : 0);
            $.ajax({
                url: api_path,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    article: article,
                    offset: Comment.getOffset(parent_id),
                    action: 'get_comment',
                    parent_id: parent_id
                },
                success: function (r)
                {
                    self.removeClass('_loading_view_more');
                    if (parent_id)
                    {
                        var list = $('#children_'+parent_id+ ' > .comment:first');
                        list.before(r.tpl.comments);
                    }
                    else
                    {
                        var list = $('.list_comments');
                        list.append(r.tpl.comments);
                    }

                    if (r.nb_comment == parseInt(self.find('.view_count').text()))
                        self.addClass('hidden');
                    self.find('.view_count').text(parseInt(self.find('.view_count').text())-r.nb_comment);
                },
                error: function () {
                    self.removeClass('_loading_view_more');
                    alert('Has error');
                }
            });
        });

        $('[data-form-comment] [name=message]').live('blur', function () {
            if (!$(this).find('input').length)
                $(this).closest('[data-form-comment]').find('[name=answer_id_customer]').val(0);
        });
    },
    answer: function(elem) {
        $('#article_comments .form_add_comment_child').remove();

        var form_add_comment_child = $('#template_form_add_comment_child').html();
        var parent_form = $(elem).closest('[data-comment]');

        var parent_id = (parent_form.data('parent-id') ? parent_form.data('parent-id') : parent_form.data('comment'));

        var html_form_add_comment_child = form_add_comment_child.split('%parent_id%').join(parent_id)
            .split('t-src').join('src')
            .split('%id_blog_article%').join(article)
            .split('%answer_id_customer%').join(parent_form.data('customer'))
            .split('%avatar%').join(comment_conf.avatar)
            .split('%answer_customer%').join('<input class="answer_customer" value="'+parent_form.data('customer-name')+'" type="button">');

        if (parent_form.is('[data-parent-id]'))
            parent_form = parent_form.closest('.comment').parent().closest('.comment').find('> [data-comment]');

        parent_form.next().after(html_form_add_comment_child);

        var form = $('.form_add_comment_child');
        if (comment_conf.logged)
        {
            form.find('[name="message"]').trigger('focus');
            form.find('[name="message"]').moveToEnd();
        }
        else
        {
            form.find('[name="email"]').trigger('focus');
        }

        $('body').animate({
            scrollTop: ($('.form_add_comment_child').offset().top - 30) + 'px'
        });
        return false;
    },
    delete: function(obj)
    {
        var elem_comment = $(obj).closest('[data-comment-id]');

        $.ajax({
            url: api_path,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'delete',
                comment: elem_comment.data('comment-id')
            },
            success: function (r)
            {
                elem_comment.find('.comment_detail').hide();
                elem_comment.prepend(r.message);
                if (elem_comment.find('.children_list_comment').length)
                    elem_comment.find('.children_list_comment').hide();
            }
        });
    },
    repair: function (obj)
    {
        var elem_comment = $(obj).closest('[data-comment-id]');

        $.ajax({
            url: api_path,
            type: 'POST',
            data: {
                ajax: true,
                action: 'repair',
                comment: elem_comment.data('comment-id')
            },
            success: function ()
            {
                elem_comment.find('.comment_detail').show();
                elem_comment.find('.comment_repair').remove();
                if (elem_comment.find('.children_list_comment').length)
                    elem_comment.find('.children_list_comment').show();
            }
        });
    },
    addMain: function (obj)
    {
        this.add(obj, 'main');
    },
    add: function(obj, type)
    {
        this.stopIntervalUpdate();
        this.abortAjax();

        var form_comment = $(obj).closest('[data-form-comment]');
        form_comment.addClass('_loading').prepend('<div class="_stage_loading"></div>');

        var data = {
            ajax: true,
            action: 'add',
            message: form_comment.find('[name=message]').text(),
            id_blog_article: form_comment.find('[name=id_blog_article]').val(),
            parent_id: form_comment.find('[name=parent_id]').val(),
            answer_id_customer: form_comment.find('[name=answer_id_customer]').val()
        };

        if (!comment_conf.logged)
        {
            data.email = form_comment.find('[name=email]').val();
            data.firstname = form_comment.find('[name=firstname]').val();
        }

        $.ajax({
            url: api_path,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (r)
            {
                if (r.hasError)
                {
                    Comment.log.error(r.errors.join('\n'));
                    form_comment.removeClass('_loading').find('._stage_loading').remove();
                }
                else
                {
                    function afterUpdate()
                    {
                        form_comment.removeClass('_loading').find('._stage_loading').remove();
                        Comment.activateIntervalUpdate();
                        if (r.create_account)
                        {
                            $('[data-create-account]').remove();
                            comment_conf.logged = true;
                            logged = true;
                        }

                        if (typeof type != 'undefined')
                            form_comment.find('[name=message]').text('');
                        else
                            form_comment.remove();
                    }

                    Comment.update(afterUpdate);
                }
            },
            error: function () {
                Comment.update();
                Comment.activateIntervalUpdate();
            }
        });
    },
    cancel: function(elem)
    {
        $(elem).closest('[data-form-comment]').remove();
        return false;
    },
    update: function (callback)
    {
        this.current_ajax = $.ajax({
            url: api_path,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                id_blog_article: article,
                action: 'update',
                ids_parent: Comment.getCommentParentIds().join('|'),
                id_last_comment: Comment.getLastIdComment()
            },
            success: function (r)
            {
                Comment.updateList(r);
                if (typeof callback != 'undefined')
                    callback();
            }
        });
    },
    updateList: function (json)
    {
        if (json.tpl.comments != '')
        {
            $('.list_comments').prepend(json.tpl.comments);
            $('.no_comments').remove();
        }
        var children = json.tpl.children;
        for (var item in children)
        {
            $('#'+item).append(children[item]);
        }
    },
    getCommentParentIds: function ()
    {
        var ids_comment = [];
        $('.list_comments > .comment').each(function () {
            ids_comment.push($(this).data('comment-id'));
        });
        return ids_comment;
    },
    getLastIdComment: function ()
    {
        var id_last_comment = 0;
        var ids_comment = [];
        $('.list_comments .comment').each(function () {
            ids_comment.push(parseInt($(this).data('comment-id')));
        });
        ids_comment.sort(function(a,b){return b-a;});
        if (ids_comment.length)
            id_last_comment = ids_comment[0];
        return id_last_comment;
    },
    getFirstIdComment: function ()
    {
        var id_first_comment = 0;
        var ids_comment = [];
        $('.list_comments .comment').each(function () {
            ids_comment.push(parseInt($(this).data('comment-id')));
        });
        ids_comment.sort(function(a,b){return a-b;});
        if (ids_comment.length)
            id_first_comment = ids_comment[0];
        return id_first_comment;
    },
    getOffset: function (parent_id)
    {
        var list = $('.list_comments');
        if (parent_id)
            list = $('#children_'+parent_id);
        return list.find('> .comment').length;
    },
    log: {
        debug: function(message)
        {
            this.message(message, 'debug');
        },
        error: function(message)
        {
            this.message(message, 'error');
        },
        success: function(message)
        {
            this.message(message, 'success');
        },
        message: function (message, type)
        {
            alert(message);
        }
    },
    abortAjax: function()
    {
        if (this.current_ajax != null)
            this.current_ajax.abort();
    },
    stopIntervalUpdate: function()
    {
        if (this._interval_update != null)
            clearInterval(this._interval_update);
    },
    activateIntervalUpdate: function()
    {
        this._interval_update = setInterval(function () {
            Comment.update();
        }, comment_conf.interval_update);
    },
    setAvatar: function (path)
    {
        comment_conf.avatar = path;
        $('.upload_avatar').closest('.wrapp_input_file').find('img').attr('src', path + '?time=' + new Date().getTime());
        self.replaceWith(self.clone(true));
    }
};
