
## NUEVOS REQUERIMIENTOS

estructura circulos de pedidos de workflow 

por el momento solo tenemos el workflows de conciliacion, que esta en http://127.0.0.1:8000/programadores/workflows/upload, eso significa que el operario cuando pida uno http://127.0.0.1:8000/operador/workflows/request elige el cliente y elige sede (FALTA SEDE EN LOS CAMPOS), cuando eliga el workflow conciliacion , que es el unico que tenemos, y completa todos los campos, los manda a enviar, y eso va a http://127.0.0.1:8000/programadores/workflows/requests, el analista ve los pedidos y puede aceptar o rechazar,  pero cuando acepte debería
ir a un nuevo formulario que esta en http://127.0.0.1:8000/programadores/workflows/upload con los datos cargados, pero no se como hacerlo, quizas una nueva vista parecida a la de upload, pero con los datos cargados, para que el operador carge los excel y los ejecutes, y ahi cambiar el estado a ejecutado, y que se pueda ver en el historial de pedidos, historia de ejecutados. me comprendes?