		<div class="comment-$wwedit-wrapper" id="comment-edit-wrapper-$id" style="display: block;">
			<form class="comment-edit-form" id="comment-edit-form-$id" action="item" method="post" >
				<input id="f-type-$id" type="hidden" name="type" value="$type" />
				<input id="f-profile-uid-$id" type="hidden" name="profile_uid" value="$profile_uid" />
				<input id="f-parent-$id" type="hidden" name="parent" value="$parent" />
				<input id="f-return-path-$id" type="hidden" name="return" value="$return_path" />

				<div class="comment-edit-photo" id="comment-edit-photo-$id" >
					<a class="comment-edit-photo-link" href="$mylink" title="$mytitle"><img class="my-comment-photo" src="$myphoto" alt="$mytitle" title="$mytitle" /></a>
				</div>
				<div class="comment-edit-photo-end"></div>
				<textarea id="comment-edit-text-$id" class="comment-edit-text-empty" name="body" onFocus="commentOpen(this,$id);" onBlur="commentClose(this,$id);" >Comment</textarea>

				<div class="comment-edit-text-end"></div>
				<div class="comment-edit-submit-wrapper" id="comment-edit-submit-wrapper-$id" style="display: none;" >
					<input type="submit" id="comment-edit-submit-$id" class="comment-edit-submit" name="submit" value="Submit" />
				</div>

				<div class="comment-edit-end"></div>
			</form>

		</div>
