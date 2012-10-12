$(function() {
	$('#Avatar').change(function() {
		var value = $(this).attr('value');

		if (value != '') {
			$('#DelAvatarBox').remove();
		}
	});

	// Wyszukiwanie użytkownika po jego loginie
	searchUser(true, true);

	// Wybieranie adresata wiadomości
	$('body').on('click', '.defender', function() {
		var id = $(this).attr('id').split('-')[1];
		window.location.href = addr_site+'admin/pages/users.php?page=users&user='+id;
	});
	// end of Wybieranie adresata wiadomości


	// Usuwanie wszystkich opcji listy z wyjątkiem domyślnej, gdyż
	// wybrane elementy listy multi będą tutaj później przypisane
	function cleanSelectOptions(id, key)
	{
		$(id+ ' option').each(function() {
			if ($(this).val() != key) {
				$(this).remove();
			}
		});
	}

	$('#user_groups option:first').attr('checked', 'checked');

	$('#InsightGroups').change(function() {

		var $object = $(this);

		var data = $object.val();

		// Pobieranie wartości `value` dotychczas wybranego elementu
		var checked = $('#user_groups').val();

		// Czyszczenie listy select
		cleanSelectOptions('#user_groups', 2);

		if (data) {
			for (i = 0; i < data.length; i++) {
				if (data[i] == checked) {
					$('#user_groups').prepend('<option value='+data[i]+' checked=checked>'+$object.find('[value='+data[i]+']').text()+'</option>');
					$('#user_groups').parent().find('span').text($object.find('[value='+data[i]+']').text());
				} else {
					$('#user_groups').prepend('<option value='+data[i]+'>'+$object.find('[value='+data[i]+']').text()+'</option>');
				}
			}

			/**
			 * Jeżeli z listy multi-select odznaczono element, który w zwykłej liście był wybranym,
			 * to trzeba z powrotem zaznaczyć domyślną opcję w pojedynczej liście select oraz zaktualizować wyświetlaną nazwę
			 * w znaczniku span, którego inny skrypt tworzy do prezentacji nazwy wybranej opcji
			 */
			if ($('#user_groups option[value='+checked+']').length == 0) {
				$('#user_groups option[value=2]').attr('checked', 'checked');
				$('#user_groups').parent().find('span').text($('#user_groups option[value=2]').text());
			}

		} else {
			// Powinna zostać tylko jedna opcja do wyboru,
			// ale w razie czego usuwamy inne
			cleanSelectOptions('#user_groups', 2);

			var $option = $('#user_groups option');

			$('#user_groups').parent().find('span').text($option.text());
			$option.attr('checked', 'checked');
		}
	});

	// Funkcja dodaje do wybranej opcji listy atrybut `checked`,
	// by był on widoczny z poziomu konsoli html
	$('#user_groups').change(function() {
		var checked = $(this).val();

		$(this).find('option').each(function() {
			if ($(this).val() != checked) {
				$(this).removeAttr('checked');
			} else {
				$(this).attr('checked', 'checked');
			}
		});
	});
});