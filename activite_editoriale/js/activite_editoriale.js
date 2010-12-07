function libelle_date(o, date_debut, date_fin)
{
	if(date_debut == "" && date_fin == "")
	{
		term_1 = "";
		term_2 = "";	
	}
	else
	{
		if(date_debut != "" && date_fin != "")
		{
			term_1 = "Du ";
			term_2 = " au ";
		}
		
		if(date_debut != "" && date_fin == "")
		{
			term_1 = "À partir du ";
			term_2 = "";
		}
		
		if(date_debut == "" && date_fin != "")
		{
			term_1 = "";
			term_2 = "Jusqu'au ";
		}
		
		$(o).html(term_1 + date_debut + term_2 + date_fin);

	}
	return false;
}