<!-- this is the HTML template -->
<script type="text/html" id="template">
	<h1>Comentários do Facebook</h1>
	<?php 

	// $limit = 6;
 //    $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
 //    $inicio = ($paged * $limit) - $limit;
	// echo $inicio  .' - '.$paged;
	?>
	<table class="widefat fixed comments" cellspacing="0">
	    <thead>
	        <tr>
	            <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
	                <label class="screen-reader-text" for="cb-select-all-1">Selecionar Tudo</label>
	                <input id="cb-select-all-1" type="checkbox">
	            </th>
	            <th scope="col" id="author" class="manage-column column-author sortable desc" style="">
	                <a href="">
	                <span>Autor</span>
	                <span class="sorting-indicator"></span>
	                </a>
	            </th>
	            <th scope="col" id="comment" class="manage-column column-comment" style="">Comentário</th>
	            <th scope="col" id="response" class="manage-column column-response sortable desc" style="">
	                <a href=""><span>Em resposta à</span><span class="sorting-indicator"></span></a>
	            </th>
	        </tr>
	    </thead>
	    <tfoot>
	        <tr>
	            <th scope="col" class="manage-column column-cb check-column" style="">
	                <label class="screen-reader-text" for="cb-select-all-2">Selecionar Tudo</label><input id="cb-select-all-2" type="checkbox">
	            </th>
	            <th scope="col" class="manage-column column-author sortable desc" style=""><a href="http://wptime.com/v1/wp-admin/edit-comments.php?comment_status=trash&amp;orderby=comment_author&amp;order=asc"><span>Autor</span><span class="sorting-indicator"></span></a>
	            </th>
	            <th scope="col" class="manage-column column-comment" style="">Comentário
	            </th>
	            <th scope="col" class="manage-column column-response sortable desc" style=""><a href="http://wptime.com/v1/wp-admin/edit-comments.php?comment_status=trash&amp;orderby=comment_post_ID&amp;order=asc"><span>Em resposta à</span><span class="sorting-indicator"></span></a>
	            </th>
	        </tr>
	    </tfoot>
	    <tbody id="the-comment-list" data-wp-lists="list:comment">
	        {{#fb}}
	        <tr id="comment-{{comment_id}}" class="comment even thread-even depth-1 approved">
	            <th scope="row" class="check-column">
	                <label class="screen-reader-text" for="cb-select-{{comment_id}}">Selecionar comentário</label>
	                <input id="cb-select-{{comment_id}}" type="checkbox" name="delete_comments[]" value="{{comment_id}}">
	            </th>
	            <td class="author column-author" style="overflow: visible;">
	                <strong><img alt="" src="{{imagem}}" class="avatar avatar-50 photo" height="50" width="50"> {{user}}</strong><br>
	                <a title="{{url_face}}" href="{{url_face}}" id="author_comment_url_10433" style="position: relative;">
	                    {{url_face}}
	                    <div class="mShot mshot-container" style="left: 209px; display: none;">
	                        <div class="mshot-arrow"></div>
	                        <img src="//s0.wordpress.com/mshots/v1/{{url_face}}?w=450&amp;r=3" width="450" class="mshot-image" style="margin: 0;">
	                    </div>
	                </a>
	                <a href="#" class="remove_url" commentid="{{comment_id}}" title="Remove this URL">x</a><br><a href="mailto:{{email}}">{{email}}
	                </a><br><a href="edit-comments.php?s={{ip}}&amp;mode=detail">{{ip}}</a>
	            </td>
	            <td class="comment column-comment" style="overflow: visible;">
	                <div class="comment-author"><strong>
	                    <img alt="" src="{{url_face}}" class="avatar avatar-50 photo" height="50" width="50"> {{user}}</strong><br><a title="{{url_face}}" href="{{url_face}}">{{url_face}}</a><br><a href="mailto:{{email}}">{{email}}</a><br>
	                    <a href="edit-comments.php?s={{ip}}&amp;mode=detail">{{ip}}</a>
	                </div>
	                <div class="submitted-on">Enviado em <a href="{{link}}#comment-{{comment_id}}">{{date}}</a></div>
	                <p>{{comentario}}</p>
	                <div id="inline-{{comment_id}}" class="hidden">
	                    <textarea class="comment" rows="1" cols="1">{{comentario}}</textarea>
	                    <div class="author-email">{{email}}</div>
	                    <div class="author">{{user}}</div>
	                    <div class="author-url">{{url_face}}</div>
	                    <div class="comment_status">1</div>
	                </div>
	                <div class="row-actions">
	                    <span class="approve">
	                    <a href="#" data-wp-lists="dim:the-comment-list:comment-{{comment_id}}:unapproved:e7e7d3:e7e7d3:new=approved" class="vim-a" title="Aprovar esse comentário">Aprovar</a></span>
	                    <span class="unapprove">
	                    <a href="#" data-wp-lists="dim:the-comment-list:comment-{{comment_id}}:unapproved:e7e7d3:e7e7d3:new=unapproved" class="vim-u" title="Rejeitar esse comentário">Rejeitar</a>
	                    </span>
	                    <span class="reply hide-if-no-js"> 
	                    | <a onclick="window.commentReply &amp;&amp; commentReply.open( '{{comment_id}}','18208' );return false;" class="vim-r" title="Responder esse comentário" href="#">Responder</a>
	                    </span>
	                    <span class="quickedit hide-if-no-js">
	                    | <a onclick="window.commentReply &amp;&amp; commentReply.open( '{{comment_id}}','18208','edit' );return false;" class="vim-q" title="Edição rápida" href="#">Edição rápida</a>
	                    </span>
	                    <span class="edit"> 
	                    | <a href="comment.php?action=editcomment&amp;c={{comment_id}}	" title="Editar comentário">Editar</a>
	                    </span>
	                    <span class="history"> 
	                    | <a href="comment.php?action=editcomment&amp;c={{comment_id}}#akismet-status" title="Ver histórico de comentários"> Histórico</a>
	                    </span>
	                    <span class="spam">
	                    | <a href="comment.php?c={{comment_id}}&amp;action=spamcomment&amp;_wpnonce=4bce57bc83" data-wp-lists="delete:the-comment-list:comment-{{comment_id}}::spam=1" class="vim-s vim-destructive" title="Marcar esse comentário como spam">Spam</a>
	                    </span>
	                    <span class="trash"> | <a href="comment.php?c={{comment_id}}&amp;action=trashcomment&amp;_wpnonce=4bce57bc83" data-wp-lists="delete:the-comment-list:comment-{{comment_id}}::trash=1" class="delete vim-d vim-destructive" title="Mover este comentário para a lixeira">Lixeira</a>
	                    </span>
	                </div>
	            </td>
	            <td class="response column-response" style="overflow: visible;">
	                <div class="response-links">
	                    <span class="post-com-count-wrapper">
	                    <a href="{{link}}/wp-admin/post.php?post=18208&amp;action=edit">{{titulo}}</a><br>
	                    <a href="{{link}}/wp-admin/edit-comments.php?p=18208" title="0 pendente(s)" class="post-com-count">
	                    <span class="comment-count">0</span>
	                    </a>
	                    </span> 
	                    <a href="{{link}}">Ver Post</a>
	                </div>
	            </td>
	        </tr>
	        {{/fb}}
	    </tbody>
	</table>
</script>
 
<!-- this div is empty on page load -->
<div class="wrap" id="comments-form">
	<div id="container"></div>
</div>
 