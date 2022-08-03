@extends('layouts.app')

@section('content')
    <!-- AddStudentModal -->
    <div class="modal fade" id="AddStudentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <ul id="saveform_errList"></ul>

                    <div class="form-group mb-3">
                        <label for="">Name</label>
                        <input type="text" class="name form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">E-mail</label>
                        <input type="text" class="email form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Phone</label>
                        <input type="text" class="phone form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Course</label>
                        <input type="text" class="course form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary add_student">Save</button>
                </div>
            </div>
        </div>
    </div>
    {{-- End-AddStudentModal --}}

    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">

                {{-- recebe a mesagem que vem do controller de sucesso da ação via ID --}}
                <div id="success_message"></div>

                <div class="card">
                    <div class="card-header">
                        <h4>Students Data
                            {{-- responsável por abrir o modal, o 'data-bs-target="#AddStudentModal"' comunica com o 'id' principal do 'modal' --}}
                            <a href="#" data-bs-toggle="modal" data-bs-target="#AddStudentModal"
                                class="btn btn-primary float-end btn-sm">Add Student</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>E-mail</th>
                                    <th>Phone</th>
                                    <th>Course</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    
    <script>
        $(document).ready(function () {

            /* chama a função criada abaixo para trazer os respectivos dados */
            fetchstudent();

            /* cria a função de buscar dados já salvos no banco e trazer para o usuário */
            function fetchstudent(){

            /* a função fetchstudent() realiza um ajax de GET, trazendo os dados do banco para uso */
                $.ajax({
                    type: "GET",                /* ação */
                    url: "/fetch-students",     /* caminho */
                    dataType: "json",           /* tipo de dado gerado */
                    /* o 'response traz os dados enviados pelo back-end' */
                    success: function (response) {
                        /* console.log(response.students); */
            
            /* criado a função 'each' que utiliza os dados enviados pelo back-end via 'response' e, através 
    de um loop, traz os dados direto na tabela, usando os objetos de 'response.students' dentro de 'item' */
                        $.each(response.students, function (key, item) { 
                             $('tbody').append('<tr>\
                                <td>'+item.id+'</td>\
                                <td>'+item.name+'</td>\
                                <td>'+item.email+'</td>\
                                <td>'+item.phone+'</td>\
                                <td>'+item.course+'</td>\
                                <td><button type="button" value="'+item.id+'" class="edit_student btn btn-primary btn-sm">Edit</button></td>\
                                <td><button type="button" value="'+item.id+'" class="delete_student btn btn-danger btn-sm">Delete</button></td>\
                                </tr>');
                        });
                    }
                });
            }

            /* função de 'click' que comunica com o 'button' de save em 'AddStudentModal' */
            $(document).on('click', '.add_student', function (e) {
                e.preventDefault();
            
            /* a var 'data' cria um array em que seus objetos recebem os valores dos 
            respectivos inputs do modal após o click */
                var data = {
                 /* objeto   -  input do modal  */
                    'name': $('.name').val(), 
                    'email': $('.email').val(),
                    'phone': $('.phone').val(),
                    'course': $('.course').val(),
                }

                /* TOKEN padrão do laravel para transportar dados via ajax */
                $.ajaxSetup({
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     }
                });

                $.ajax({
                    type: "POST",      /* ação */
                    url: "/students",  /* caminho */
                    data: data,      /* variavel utilizada */ /* BUB RESOLVIDO- data estava entre aspas duplas */
                    dataType: "json",  /* tipo de dado gerado */

                    /* se a ação do ajax for bem sucedida, prossegue com esta resposta */
                    success: function (response) {

                        /* caso ocorra erro no trafego dos dados */
                        if(response.status == 400){

                            /* ação que limpa a lista sempre antes de mostrar erros */
                            $('#saveform_errList').html("");

                            /* ação que adiciona a esta 'ul' a class de alert (css) */
                            $('#saveform_errList').addClass('alert alert-danger');

                            /* este 'each' gera um loop para listar os erros */
                            /* os parametros 'status' e 'erros' vem do controller, da classe 'Validator' */
                            $.each(response.errors, function (key, err_values) { 

                                /* este append é gerado dentro do corpo do AddModal listando os erros */
                                 $('#saveform_errList').append('<li>' + err_values +'</li>');
                            });

                            
                        /* se não */    
                        }else{

                            /* esvazia a tag HTML que contém este ID */
                            $('#saveform_errList').html("");

                            console.log('teste');

                            /* a tag HTML que contem este ID recebe a class de alert success */
                            $('#success_message').addClass('alert alert-success');

                            /* a tag HTML que contem este ID recebe em seu text, via 
                            response do controller, a 'message' de sucesso da ação
                            em seguida envia para a tag HTML que contem este ID */
                            $('#success_message').text(response.message);

                            /* esconde o modal que contem este ID */
                            $('#AddStudentModal').modal('hide');

                            /* limpa os campos dos inputs deste modal */
                            $('#AddStudentModal').find('input').val("");
                        }
                    }
                });
                
            });
        });
    </script>

@endsection
