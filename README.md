
| Método HTTP | Caminho               | Método do Controller | Finalidade               |
|-------------|-----------------------|-----------------------|--------------------------|
| GET         | /products             | index                | Listar produtos          |
| GET         | /products/create      | create               | Formulário de criação    |
| POST        | /products             | store                | Salvar novo produto      |
| GET         | /products/{product}   | show                 | Exibir produto específico|
| GET         | /products/{product}/edit | edit              | Formulário de edição     |
| PUT/PATCH   | /products/{product}   | update               | Atualizar produto        |
| DELETE      | /products/{product}   | destroy              | Deletar produto          |

---
