<template>
<div>
  <v-data-table
    :headers="headers"
    :items="student"
    class="elevation-1"
    hide-default-footer
    :items-per-page="itemsPerPage"
    :loading="loading"
    :page.sync="page"
    @page-count="pageCount = $event"
    :server-items-length="total_student"
    @pagination="fetchStudentData"
  >
    <template v-slot:top>
        <v-toolbar
        flat
        >
            <v-toolbar-title>Student Record</v-toolbar-title>
            <v-divider
            class="mx-4"
            inset
            vertical
            ></v-divider>
            <v-spacer></v-spacer>
                <v-text-field
                label="Search"
                filled
                rounded
                
                dense
                style="padding-top: 40px; padding-right: 10px;"
                v-model="search"
                :items='student'
                ></v-text-field>
            <v-btn
            v-model="dialog"
            dark
            class="mt-3"
            @click="admission()"
            >
                <v-icon dark >
                mdi-plus
                </v-icon>
            </v-btn>
        </v-toolbar>
    </template>
   
  </v-data-table>
  <div class="text-center pt-2">
    <v-pagination
    v-model="page"
    :total-visible="7"
    :length="pageCount"
    ></v-pagination>
    </div>
    <!-- <ADD 
    :dialog="dialog"
    @close="close()"
    @reload="indexStudent()"
    /> -->
    <Update
    :dialog="dialog2"
    @close="close()"
    @reload="indexStudent()"
    :id="selected_item_id"
    />
    
</div>
</template>

<script>
import {StudentPagination, searchStudent } from '@/repositories/registrar.api'
import Update from './drawer/edit.vue'
// import ADD from './drawer/add-enrollee.vue'
  export default {
    components: {
        // ADD
        Update
      },
    data: () => ({
      dialog: false,
      dialogDelete: false,
      dialog2: false,
      headers: [
        {
          text: 'ID Number',
          align: 'start',
          sortable: false,
          value: 'idnumber',
        },
        { text: 'Name', value: 'lastname', align: 'start', sortable: false  },
        { text: '', value: 'firstname', sortable: false },
        { text: 'Gender', value: 'gender' },
        { text: 'Course/Program', value: 'course' },
        { text: 'Year', value: 'year' },
        { text: 'Address', value: 'address' },
        { text: 'Contact', value: 'contact' },
      ],
      student: [],
      search : '',
      pageCount: 0,
      itemsPerPage:null,
      total_student:0,
      page:1,
      current_page:0,
      loading: false,
      selected_item_id:0,
      payload : {
        firstname : ''
      }
      
    }),
    watch: {
       "search": {
        handler(val) {
          this.indexStudent(val)
        },
        deep: true,
      },
    },
   

   

    methods: {
      admission(){
        this.$router.push({ name: "add-enrollee"});
      },
      searchUser(key) {
        if (this.timer) {
          clearTimeout(this.timer);
          this.timer = null;
        }
        this.timer = setTimeout(() => {
          searchStudent({searchkey:key}).then((response) => {
            this.student = response.data 
          }).catch((errors) => {
            console.log(errors)
          });
        },800);
      },
      
       set_data_fromServer(data) {
            this.student = data.data
            this.total_student = data.total
            this.itemsPerPage = data.per_page
            this.pageCount = data.last_page
        },
         fetchStudentData(page){
            this.current_page = page.page
            this.indexStudent()
        },
        indexStudent() {
          this.url = 'student/pagination?page='+this.current_page+ '&keyword=' +this.search
          this.loading = true
          if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
          }
          this.timer = setTimeout(() => { 
            StudentPagination(this.url).then(({data}) => {
              console.log(this.url,"index")
              this.set_data_fromServer(data)
              this.loading = false
            })
          }, 800);
        },
        edit(id){
          this.selected_item_id = id
          this.$nextTick(() => {
            this.dialog2 = true
        })
        },
        close(){
          this.dialog2 = false
          this.indexStudent()
          
        }
    },
  }
</script>