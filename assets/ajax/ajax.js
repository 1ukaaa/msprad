function MakeRequeteAjax(type,url,data,succesFunction,errorFunction,parametresOptionnels)
{
  GetRequeteAjax({type,url,data})
  .then(reponse => SuccessAjax(reponse,succesFunction,parametresOptionnels))
  .catch(reponse => ErrorAjax(reponse,errorFunction,parametresOptionnels))
}
function GetRequeteAjax(parametresAjaxJSON)
{
  return $.ajax(parametresAjaxJSON)
}
function SuccessAjax(reponse,succesFunction,parametresOptionnels)
{
  if(succesFunction !== undefined)
  {
    succesFunction(reponse,parametresOptionnels)
  }
}
function ErrorAjax(reponse,errorFunction,parametresOptionnels)
{
  if(errorFunction !== undefined)
  {
    errorFunction(reponse,parametresOptionnels)
  }
}