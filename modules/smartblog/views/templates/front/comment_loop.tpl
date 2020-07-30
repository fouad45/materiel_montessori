{if $comment.id_smart_blog_comment != ''}
<ul class="commentList">
{*
    {l s='Jan' mod='smartblog'}
    {l s='Feb' mod='smartblog'}
    {l s='Mar' mod='smartblog'}
    {l s='Apr' mod='smartblog'}
    {l s='May' mod='smartblog'}
    {l s='Jun' mod='smartblog'}
    {l s='Jul' mod='smartblog'}
    {l s='Aug' mod='smartblog'}
    {l s='Sep' mod='smartblog'}
    {l s='Oct' mod='smartblog'}
    {l s='Nov' mod='smartblog'}
    {l s='Dec' mod='smartblog'}
*}
    <div id="comment-{$comment.id_smart_blog_comment}">
                                        <li class="even">
                                          <img class="avatar" alt="Avatar" src="{$link->getMediaLink($smarty.const._MODULE_DIR_)}/smartblog/images/avatar/avatar-author-default.jpg">
                                          <div class="name">{$childcommnets.name}</div>
                                          <div class="created">
                                             <span itemprop="commentTime">{l s=$childcommnets.created|date_format:M mod='smartblog'}&nbsp;{$childcommnets.created|date_format:d},&nbsp;{$childcommnets.created|date_format:Y}</span>
                                          </div>
                                          <p>{$childcommnets.content}</p>
                                          {if Configuration::get('smartenablecomment') == 1}
                                            {if $comment_status == 1}
                            <div class="reply">
                                   <a onclick="return addComment.moveForm('comment-{$comment.id_smart_blog_comment}', '{$comment.id_smart_blog_comment}', 'respond', '{$smarty.get.id_post}')"  class="comment-reply-link">{l s='Reply' mod='smartblog'}</a>
                             </div>
                                            {/if}
                                          {/if}
                        {if isset($childcommnets.child_comments)}
                            	{foreach from=$childcommnets.child_comments item=comment}  
                                   {if isset($childcommnets.child_comments)}
                        {include file="modules/smartblog/views/templates/front/comment_loop.tpl" childcommnets=$comment}
                                    {$i=$i+1}
                                    
                                        {/if}
                                {/foreach}
                         {/if}
                                        </li>
    </div>
</ul>
                                        {/if}
                                        
                                        