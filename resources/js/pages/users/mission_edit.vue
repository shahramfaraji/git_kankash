<template>
    <div class="row me-2 pt-4">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="row bg-white border border-2 rounded-3 border-dark">
            <div class="col-md-4 text-center p-2 border_L_1">
                <img src="/logo.jpg" width="90px">
            </div>
            <div class="col-md-4 border_L_1 text-center padding_25">
                درخواست پرداخت فوق العاده<br> ماموریت و هزینه سفر کارکنان
            </div>
            <div class="col-md-4 p-4">
                <div >تاریخ: <span style="display: inline-block;">{{ form.date }}</span></div><br>
                <div>شماره: {{ this.$route.params.id }}</div>
            </div>
        </div>
        <div class="row bg-white border border-2 rounded-3 border-dark mt-1 p-2">
            <div calss="col-md-12">
                نام و نام خانوادگی: {{ this.$route.params.name }}
            </div>
        </div>
        <div class="row bg-white border border-2 rounded-3 border-dark mt-1">
            <div class="col-md-3 border_L_1 padding_25">
                آغاز ماموریت: <span style="display: inline-block;">{{ form.date }}</span><br>
            </div>
            <div class="col-md-3 border_L_1 padding_25">
                پایان ماموریت: <span style="display: inline-block;">{{ form.date }}</span><br>
            </div>
            <div class="col-md-3 p-4 border_L_1 padding_25">
               مدت ماموریت: 1 روز
            </div>
            <div class="col-md-3 p-4">
               محل ماموریت: <span v-for="item in data.city"><span v-if="item.id==form.city">{{ item.name }}</span></span> - <span v-for="item in data.company"><span v-if="item.id==form.company">{{ item.name }}</span></span>
            </div>
        </div>
        <div class="row bg-white border border-2 rounded-3 border-dark mt-1 p-2">
            <div calss="col-md-12" style="position: relative;">
                <font-awesome-icon
                style="position: absolute;left: 0px;"
                  class="text-warning left"
                  :icon="icons.faEdit"
                  :fixed-width="true"
                  @click="openModal()"
                />    
                فوق العاده ماموریت و هزینه های قابل پرداخت:<br><br>
                <div>
                    1: فوق العاده ماموریت: بابت 1 روز از قرار روزی<span>
                    {{ format_price(mission_price.price) }}
                </span>ریال
                </div><br>
                <div>
                    2: هزینه ایاب و ذهاب:<br><br>
                    بابت <span v-for="item in data.city"><span v-if="item.id==form.city">{{ item.KM }}</span></span> کیلومتر با وسیله ی نقلیه شخصی مبلغ <span>{{ format_price(form.price) }}</span> ریال
                </div>

            </div>
        </div>
        <div class="row bg-white border border-2 rounded-3 border-dark mt-1 p-2">
            <div calss="col-md-12">
                جمع مبلغ قابل پرداخت: {{ format_price((form.price) + mission_price.price) }} ریال
            </div><br><br><br>
            <div class="col-md-4 text-center">
                محل امضاء مامور
            </div>
            <div class="col-md-4 text-center">
                محل امضاء مدیر واحد مالی
            </div>
            <div class="col-md-4 text-center">
                محل امضاء مدیر عامل
            </div><br><br><br>
        </div>
        <!-- Modal -->
    <transition name="fade">
      <div
        v-if="modalVisible"
        class="modal fade show"
        style="display: block"
        tabindex="-1"
        role="dialog"
        @click="closeModalOutside"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                هزینه ماموریت
              </h1>
            </div>
            <div class="modal-body">
              <table class="table">
                <tbody>
                  <tr>
                    <td>
                        <form
                          @submit.prevent="conf_mission_price"
                          @keydown="mission_price.onKeydown($event)"
                        >
                          <div class="row">
                            <div class="col-md-9">
                              <input
                                v-model="mission_price.price"
                                :class="{
                                  'is-invalid': mission_price.errors.has('price'),
                                }"
                                class="form-control"
                                type="number"
                                name="price"
                              />
                              <has-error :form="mission_price" field="price" />
                            </div>
                            <div class="col-md-3">
                              <!-- Submit Button -->
                              <v-button :loading="mission_price.busy">
                                {{ $t("تایید") }}
                              </v-button>

                              <!-- GitHub Register Button -->
                              <login-with-github />
                            </div>
                          </div>
                        </form>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeModal"
              >
                بستن
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-1"></div>
      </div>
    </transition>
        </div>
    </div>
  </template>
  
  <script>
  import Form from "vform";
  import LoginWithGithub from "~/components/LoginWithGithub";
  import axios from "axios";
  import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
  import {
    faEdit,
    faTrash,
    faInfoCircle,
    faPrint,
  } from "@fortawesome/free-solid-svg-icons";
  import moment from "jalali-moment";
  import Select2 from "v-select2-component";
  
  export default {
    components: {
      LoginWithGithub,
      FontAwesomeIcon,
      Select2,
    },
  
    middleware: "admin",
  
    metaInfo() {
      return { title: this.$t("ثبت نام") };
    },
  
    data: () => ({
      icons: {
        faEdit,
        faTrash,
        faInfoCircle,
        faPrint,
      },
      form_filter: new Form({
        id: null,
        start: null,
        end: null,
      }),
      mission_price: new Form({
        price: null,
      }),
      form: new Form({
        id: null,
        date: null,
        city: null,
        company: null,
        more: null,
        price: null,
        alongs: [],
      }),
      data: [],
      mustVerifyEmail: false,
      modalVisible: false,
      selectedRow: null,
      x: [],
    }),
    mounted() {
      this.fetchData();
    },
    methods: {
      openModal() {
        this.selectedRow = this.$route.params.id;
        this.modalVisible = true;
      },
      closeModal() {
        this.modalVisible = false;
      },
      closeModalOutside(event) {
        // بستن مودال اگر کلیک بروی پس‌زمینه مودال اتفاق بیفتد
        if (event.target === event.currentTarget) {
          this.closeModal();
        }
      },
      format_price(price) {
        return new Intl.NumberFormat('en-US').format(price);
      },
      async conf_mission_price() {
        const id = this.$route.params.id;
        const { data } = await this.mission_price.post(
            "/api/edit_service_mission_price_admin/" + id
        );
        // Must verify email fist.
        if (data.status) {
            this.mustVerifyEmail = true;
        } else {
            this.fetchData();
            this.closeModal();
        }
      },
      convertToJalali(date) {
        const jalaliMoment = moment(date, "YYYY-MM-DD").locale("fa");
        let jalaliDate = jalaliMoment.format("YYYY-MM-DD");
        return jalaliDate;
      },
      async fetchData() {
        await axios
          .get("/api/service_edit_admin/" + this.$route.params.id)
          .then((response) => {
            console.log(response.data.service)
            this.data = response.data;
            this.form.date = this.convertToJalali(response.data.service.date);
            this.form.city = response.data.service.city_id;
            this.form.company = response.data.service.company_id;
            this.form.more = response.data.service.more;
            this.form.price = response.data.service.price;
            this.mission_price.price = response.data.service.mission_price;
            for (let i = 0; i < response.data.service.along.length; i++) {
              this.form.alongs.push(
                response.data.service.along[i].along_id.toString()
              );
            }
          })
          .catch((error) => {
            console.error("Error fetching users", error);
          });
      },
      async create() {
        // Register the user.
        const { data } = await this.form.post(
          "/api/edit_service_admin/" + this.$route.params.id
        );
        // Must verify email fist.
        if (data.status) {
          this.mustVerifyEmail = true;
        } else {
          this.$router.push({
            name: "users_more",
            params: {
              id: this.$route.params.userid,
              name: this.$route.params.name,
            },
          });
        }
      },
      async deleteuser(id) {
        const { data } = await this.form.post("/api/delete_user/" + id);
        if (data.status) {
          this.mustVerifyEmail = true;
        } else {
          this.fetchData();
          this.closeModal();
        }
      },
    },
  };
  </script>
  