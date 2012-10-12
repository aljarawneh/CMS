{php} opentable(__('Wyszukiwarka')) {/php}
	<form id="This" action="{$URL_REQUEST}" method="post" />
		<div class="tbl1">
			<div class="formLabel sep_1 grid_3"><label for="search_text">{i18n('Wpisz szukaną frazę:')}</label></div>
			<div class="formField grid_7"><input type="text" name="search_text" value="{$search_text}" id="search_text" /></div>
		</div>
		<div class="tbl2">
			<div class="formLabel sep_1 grid_3">{i18n('Miejsce wyszukiwania:')}</div>
			<div class="formField grid_7">
				<label for="news"><input type="radio" id="news" name="search_type" value="news"{if $search_type === "news"} checked="checked"{/if}>{i18n('Newsy')}</label>
				<label for="users"><input type="radio" id="users" name="search_type" value="users"{if $search_type === "users"} checked="checked"{/if}>{i18n('Użytkownicy')}</label>
				<label for="tags"><input type="radio" id="tags" name="search_type" value="tags"{if $search_type === "tags"} checked="checked"{/if}>{i18n('Tagi')}</label>
				<label for="all"><input type="radio" id="all" name="search_type" value="all"{if $search_type === "all"} checked="checked"{/if}>{i18n('Cała strona')}</label>
			</div>
		</div>
		
		<div class="tbl Buttons">
			<div class="center grid_2">
				<input type="hidden" name="search" value="yes" />
				<span id="SendForm_This" class="Save button"><strong>{i18n('Search')}</strong></span>
			</div>
		</div>
	</form>
	
	{if $message}<div class="{$class}">{$message}</div>{/if}
	
	{if $all}
		<h4>Newsy</h4>
	{/if}
	{if $search_type === "news" && $news || ($all && $news !== array())}
		<div class="tbl">
			<div class="grid_4 bold">Tytuł</div>
			<div class="grid_2 bold">Autor</div>
			<div class="grid_2 bold">Kategoria</div>
			<div class="grid_2 bold">Data dodania</div>
		</div>
		{section=news}
			<div class="{$news.row_color}">
				<div class="grid_4">{$news.i}. <a href="{$news.news_link}">{$news.title}</a></div>
				<div class="grid_2"><a href="{$news.author_link}">{$news.author}</a></div>
				<div class="grid_2"><a href="{$news.category_link}">{$news.category}</a></div>
				<div class="grid_2">{$news.date}</div>
			</div>
			<div class="{$news.row_color}">
				<div class="grid_10">
					<p class="justify">{$news.content}</p>
					{if $content_extended}
						<p class="justify">{$news.content_extended}</p>
					{/if}
				</div>
			</div>
		{/section}
	{elseif $search_type === "news" || ($all && $news === array())}
		<div class="info">Nie znaleziono newsów pasujących do następującego kryterium "<strong>{$search_text}</strong>"</div>
	{/if}
	
	{if $all}
		<h4>Tagi</h4>
	{/if}
	{if $search_type === "tags" && $tags || ($all && $tags !== array())}
		<div class="tbl">
			<div class="grid_10 bold">Tag</div>
		</div>
		{section=tags}
			<div class="{$tags.row_color}">
				<div class="grid_1">{$tags.i}.</div>
				<div class="grid_9"><a href="{$tags.link}">{$tags.value}</a></div>
			</div>
		{/section}
	{elseif $search_type === "tags" || ($all && $users === array())}
		<div class="info">Nie znaleziono tagów pasujących do następującego kryterium "<strong>{$search_text}</strong>"</div>
	{/if}
	
	{if $all}
		<h4>Użytkownicy</h4>
	{/if}
	{if $search_type === "users" && $users || ($all && $users !== array())}
		<div class="tbl">
			<div class="grid_5 bold">Nazwa użytkownika</div>
			<div class="grid_2 bold">Grupa</div>
			<div class="grid_3 bold">Ostatnia wizyta</div>
		</div>
		{section=users}
			<div class="{$users.row_color}">
				<div class="grid_1">{$users.i}.</div>
				<div class="grid_4"><a href="{$users.link}">{$users.username}</a></div>
				<div class="grid_2">{$users.role}</div>
				<div class="grid_3">{$users.visit}</div>
			</div>
		{/section}
	{elseif $search_type === "users" || ($all && $users === array())}
		<div class="info">Nie znaleziono użytkowników pasujących do następującego kryterium "<strong>{$search_text}</strong>"</div>
	{/if}
{php} closetable() {/php}
