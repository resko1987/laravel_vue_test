<template>
    <div class="block-employes">
        <div class="errors">
            {{ error }}
        </div>
        <div class="mb-3 row">

            <div class="col-auto d-flex align-items-center">
                Отдел:
            </div>
            <div class="col-auto">
                <select name="find_department" class="form-select">
                    <option value="0" v-on:click="find_department=0;init_employes();">---</option> 
                    <option v-for="(v, k) in departments" :value="v.id" v-on:click="find_department=v.id;init_employes();">{{ v.title }}</option> 
                </select>
            </div>
            <div class="col">
                <input type="button" value="Добавить" class="btn btn-success float-end" v-on:click="edit(0);" data-bs-toggle="modal" data-bs-target="#blockEmployeEdit" />
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr class="table-light text-center">
                    <th>ФИО</th>
                    <th>Отдел</th>
                    <th>Должность</th>
                    <th>Руководитель</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(v, k) in employes">
                    <td>{{ v.fio }}</td>
                    <td class="text-center">{{ v.department_title }}</td>
                    <td class="text-center">{{ v.job_title }}</td>
                    <td class="text-center">{{ v.director }}</td>
                    <td class="text-center"><a href="javascript:void(0)" v-on:click="edit(v.id);" data-bs-toggle="modal" data-bs-target="#blockEmployeEdit">edit</a></td>
                    <td class="text-center"><a href="javascript:void(0)" v-on:click="delete_employe(v.id);">del</a></td>
                </tr>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item" v-for="(v, k) in pagination_count">
                    <a class="page-link" :class="[(k==page) ? 'active' : '' ]" v-on:click="page=k;init_employes();">{{ i = k + 1 }}</a>
                </li>
            </ul>
        </nav>

        <div class="modal block-employe-edit" tabindex="-1" id="blockEmployeEdit">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Упарвление</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ФИО</label>
                            <input type="text" class="form-control" name="fio" v-model="employe_fio" placeholder="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Отдел</label>
                            <select name="department" class="form-select" v-model="employe_department_id">
                                <option value="0" v-on:click="find_department=0;init_employes();">---</option> 
                                <option v-for="(v, k) in departments" :value="v.id">{{ v.title }}</option> 
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Должность</label>
                            <select name="job" class="form-select" v-model="employe_job_id">
                                <option value="0" v-on:click=";init_employes();">---</option> 
                                <option v-for="(v, k) in jobs" :value="v.id">{{ v.title }}</option> 
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Руководитель</label>
                            <input type="text" class="form-control" v-model="find_director" v-on:change="find_directors();" name="find_director" placeholder="Поиск руководителя">
                            <select name="parent_id" class="form-select" v-model="employe_parent_id">
                                <option value="0" v-on:click=";init_employes();">---</option> 
                                <option v-for="(v, k) in directors" :value="v.id">{{v.fio}}</option> 
                            </select>
                        </div>
                        <div>{{ error }}</div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" value="0" v-model="employe_id" />
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id='closeModalblockEmployeEdit'>Отмена</button>
                        <button type="button" class="btn btn-primary" v-on:click="update_employe();">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                max_row: 5,
                pagination_count: 0,
                page: 0,
                employes: [],
                departments: [],
                directors: [],
                jobs: [],
                find_department: 0,
                error: '',
                loading: true,
                find_director: '',
                employe_id: 0,
                employe_fio: '',
                employe_department_id: 0,
                employe_job_id: 0,
                employe_parent_id: 0,
            }
            ;
        },
        created() {
        },
        mounted() {
            this.init_employes();
            this.all_departments();
            this.all_jobs();
        },
        updated() {
            
        },
        renderTracked() {
            console.log('renderTracked');
        },
        methods: {
            /**
             * Данные по сотрудникам
             */
            init_employes() {
                axios
                        .get('/employes/all/?page=' + this.page + '&find=department=' + this.find_department)
                        .then(response => {
                            if (response.status === 200) {
                                this.employes = response.data.employes;
                                this.pagination_count = 0;
                                if (response.data.employes_count > 0) {
                                    this.pagination_count = Math.ceil(response.data.employes_count / this.max_row);
                                }
                            } else {
                                console.log(response);
                                this.error = response.data;
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.error = error;
                        })
                        .finally(() => (this.loading = false));
            },

            get_employe(id) {
                axios
                        .get('/employes/' + id + '/')
                        .then(response => {
                            if (response.status === 200) {
                                if (response.data.employe[0]) {
                                    this.employe_id = response.data.employe[0]['id'];
                                    this.employe_fio = response.data.employe[0]['fio'];
                                    this.employe_department_id = response.data.employe[0]['department_id'];
                                    this.employe_job_id = response.data.employe[0]['job_id'];
                                    this.employe_parent_id = response.data.employe[0]['parent_id'];
                                    console.log('fio: ' + this.employe_fio);
                                }
                            } else {
                                console.log(response);
                                this.error = response.data;
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.error = error;
                        })
                        .finally(() => (this.loading = false));
            },

            /*
             * Департаменты
             */
            all_departments() {
                axios
                        .get('/departments/all/')
                        .then(response => {
                            if (response.status === 200) {
                                this.departments = response.data.departments;
                            } else {
                                this.error = response.data;
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.error = error;
                        })
                        .finally(() => (this.loading = false));
            },
            /*
             * Руководители
             */
            all_jobs() {
                axios
                        .get('/jobs/all/')
                        .then(response => {
                            if (response.status === 200) {
                                this.jobs = response.data.jobs;
                            } else {
                                this.error = response.data;
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.error = error;
                        })
                        .finally(() => (this.loading = false));
            },

            find_directors() {
                if (this.find_director.length > 3) {
                    axios
                            .get('/employes/all/?find=find_director_name=' + this.find_director)
                            .then(response => {
                                if (response.status === 200) {
                                    this.directors = response.data.employes;
                                } else {
                                    this.error = response.data;
                                }
                            })
                            .catch(error => {
                                console.log(error);
                                this.error = error;
                            })
                            .finally(() => (this.loading = false));
                }
            },
            edit(id) {
                this.employe_id = id;
                console.log('id: ' + this.employe_id);
                if (id > 0) {
                    this.get_employe(id);
                } else {
                    this.clearForm();
                }
            },
            clearForm() {
                this.employe_id = 0;
                this.employe_fio = '';
                this.employe_department_id = 0;
                this.employe_job_id = 0;
                this.employe_parent_id = 0;
            },
            update_employe() {
                console.log('update_employe ' + this.employe_id);
                this.error = '';
                if (this.employe_fio === undefined || this.employe_fio.length < 3) {
                    this.error = 'Поле ФИО не заполено!';
                }
                if (this.employe_department_id === undefined || this.employe_department_id === 0) {
                    this.error = 'Поле Отдел не заполено!';
                }
                if (this.employe_job_id === undefined || this.employe_job_id === 0) {
                    this.error = 'Поле Должность не заполено!';
                }
                if (this.error.length === 0) {
                    axios
                            .post('/employes/update/', null, {
                                params: {
                                    id: this.employe_id,
                                    fio: this.employe_fio,
                                    department_id: this.employe_department_id,
                                    job_id: this.employe_job_id,
                                    parent_id: this.employe_parent_id,
                                }
                            })
                            .then(response => {
                                if (response.status === 200 && response.data.status === 0) {
                                    const modal = document.getElementById("closeModalblockEmployeEdit");
                                    modal.click();
                                    this.clearForm();

                                    this.init_employes();
                                } else {
                                    this.error = response.data;
                                }
                            })
                            .catch(error => {
                                console.log(error);
                                this.error = error;
                            })
                            .finally(() => (this.loading = false));
                }
            },
            delete_employe(id) {
                if (confirm('Вы уверены что хотите удалить запись?')) {
                    axios
                            .get('/employes/del/' + id + '/')
                            .then(response => {
                                if (response.status === 200 && response.data.status === 0) {
                                    this.init_employes();
                                } else {
                                    this.error = response.data;
                                }
                            })
                            .catch(error => {
                                console.log(error);
                                this.error = error;
                            })
                            .finally(() => (this.loading = false));
                }
            }
        }
    }
</script>
<style>
    .pagination .page-link:hover{
        cursor: pointer;
    }
    .border_red{
        border: 2px solid red;
    }
</style>
