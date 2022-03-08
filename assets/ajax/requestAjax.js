function addAjax(url, data, succesFunction, errorFunction, parametresOptionnels)
{
  let type = 'POST';
  MakeRequeteAjax(type, url, data, succesFunction, errorFunction, parametresOptionnels);
}
