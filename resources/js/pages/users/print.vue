<template>
  <div>
    <div class="row" id="btn_print">
      <div class="col-md-12 m-auto mt-3">
        <button
          type="button"
          class="btn btn-outline-primary"
          @click="openModal"
        >
          فیلتر
        </button>
        <button type="button" class="btn btn-outline-primary" v-print>
          پرینت
        </button>
        <button
          type="button"
          class="btn btn-outline-primary"
          @click="exportToExcel"
        >
          اکسل
        </button>
        <button type="button" class="btn btn-outline-success" @click="_pay">
          تبدیل به پرداخت شده
        </button>
        <button type="button" class="btn btn-outline-danger" @click="_notpay">
          تبدیل به پرداخت نشده
        </button>
      </div>
      <div class="col-md-12 mt-1 mb-1" v-if="showText">
        <small class="text-danger">
          {{ errotText }}
        </small>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 mt-3 mb-5">
        <center>
          <div><span>شرکت کنکاش رایانه الوند</span></div>
          <div><span>"گزارش هزینه ایاب و ذهاب"</span></div><br>
        </center>
        <card :title="$t('سرویس های ' + this.$route.params.name)">
          <table class="table table-bordered table_print" id="table-1">
            <thead>
              <tr>
                <th>#</th>
                <th>تاریخ</th>
                <th>شهر</th>
                <th>سازمان</th>
                <th>همراهان</th>
                <th>هزینه(ریال)</th>
                <th>تاریخ پرداخت</th>
                <th>وضعیت</th>
                <th>توضیحات</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in data.service" :key="item.id">
                <td>
                  <input
                    type="checkbox"
                    class="form-check-input"
                    id="exampleCheck1"
                    v-model="pay.data"
                    :value="item.id"
                  />
                </td>
                <td>
                  {{ convertToJalali(item.date) }}
                </td>
                <td>
                  <span v-if="item.city != null">{{ item.city.name }}</span>
                </td>
                <td>
                  <span v-if="item.company != null">{{
                    item.company.name
                  }}</span>
                </td>
                <td>
                  <span v-if="item.along != null">
                    <span v-for="(a, index) in item.along" :key="a.id">
                      <span v-if="index >= 1">-</span> {{ along_item(a.along_id) }} 
                    </span>
                  </span>
                </td>
                <td>{{ format_price(item.price) }}</td>
                <td>
                  <span v-if="item.pay != 1" class="text-danger">
                   
                  </span>
                  <span v-else class="text-success"> 
                    <span v-if="item.pay_date != null">
                      {{ convertToJalali(item.pay_date) }} 
                    </span>
                  </span>
                </td>
                <td>
                  <span v-if="item.pay != 1" class="text-danger">
                    پرداخت نشده
                  </span>
                 <span v-else class="text-success"> پرداخت شده </span>
                </td>
                <td>
                  {{ item.more }}
                </td>
              </tr>
              <tr
                v-if="data.service == 0 || data.service == undefined"
                class="iran"
              >
                <td colspan="8" class="text-center">موردی یافت نشد</td>
              </tr>
            </tbody>
          </table>
          <div class="text-danger">
            <small>
              جمع هزینه پرداخت نشده(ریال): {{ format_price(total_notpay) }}
            </small>
          </div><br>
          <!--<div class="text-danger">
            <small>
              جمع هزینه پرداخت شده(ریال): {{ format_price(total) }}
            </small>
          </div><br> -->
          <div>
            جمع کل هزینه(ریال): {{ format_price(total) }}
          </div>
        </card>
      </div>
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">فیلتر</h1>
              </div>
              <div class="modal-body">
                <form
                  @submit.prevent="filter"
                  @keydown="form.onKeydown($event)"
                >
                  <!-- Name -->
                  <div class="mb-3 row">
                    <label class="col-md-4 mb-2 col-form-label text-md-end">{{
                      $t("شروع تاریخ")
                    }}</label>
                    <div class="col-md-8 mb-2">
                      <date-picker
                        v-model="form_filter.start"
                        simple
                        auto-submit
                        :class="{ 'is-invalid': form.errors.has('start') }"
                        class="form-control"
                      />
                      <has-error :form="form_filter" field="start" />
                    </div>
                    <label class="col-md-4 mb-2 col-form-label text-md-end">{{
                      $t("شروع پایان")
                    }}</label>
                    <div class="col-md-8 mb-2">
                      <date-picker
                        v-model="form_filter.end"
                        simple
                        auto-submit
                        :class="{ 'is-invalid': form.errors.has('end') }"
                        class="form-control"
                      />
                      <has-error :form="form_filter" field="end" />
                    </div>
                    <label class="col-md-4 mb-2 col-form-label text-md-end">{{
                    $t("پرداخت نشده")
                    }}</label>
                    <div class="col-md-8 mb-2">
                      <div
                        class="form-check form-check d-flex justify-content-start"
                      >
                        <input
                          v-model="form_filter.price"
                          class="form-check-input float-right mt-3"
                          type="checkbox"
                          id="adminCheckbox"
                          name="price"
                        />
                        <has-error :form="form_filter" field="price" />
                      </div>
                    </div>
                  </div>

                  <div class="mb-3 row">
                    <div class="col-md-7 offset-md-3 d-flex">
                      <!-- Submit Button -->
                      <v-button :loading="form_filter.busy">
                        {{ $t("ثبت") }}
                      </v-button>

                      <!-- GitHub Register Button -->
                      <login-with-github />
                    </div>
                  </div>
                </form>
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
import VueHtmlToPaper from "vue-html-to-paper";
import print from "vue-print-nb";
import moment from "jalali-moment";
import VueSimpleAlert from "vue-simple-alert";
import * as XLSX from "xlsx";



export default {
  components: {
    LoginWithGithub,
    FontAwesomeIcon,
    VueSimpleAlert,
  },

  middleware: "admin",
  directives: {
    print,
  },
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
      price: null
    }),
    form: new Form({
      id: null,
      date: null,
      city: null,
      company: null,
      more: null,
      price: null,
    }),
    pay: new Form({
      data: [],
    }),
    form_price: new Form({
      price: null,
    }),
    data: [],
    mustVerifyEmail: false,
    modalVisible: false,
    selectedRow: null,
    showText: false,
    errotText: null,
    total: 0,
    total_notpay: 0,
  }),
    mounted() {
    this.fetchData();
  },

    
  methods: {
    along_item(id) {
      const foundUser = this.data.user.find((item) => item.id === id);
      return foundUser.text;
    },
    openModal() {
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
    convertToJalali(date) {
      const jalaliMoment = moment(date, "YYYY-MM-DD").locale("fa");
      let jalaliDate = jalaliMoment.format("YYYY-MM-DD");
      return jalaliDate;
    },
    printTable() {
      this.$htmlToPaper("printMe");
    },
    exportToExcel() {
      const tableData = [
        [
          "کاربر",
          "تاریخ میلادی",
          "تاریخ شمسی",
          "شهر",
          "سازمان",
          "همراهان",
          "هزینه",
          "وضعیت",
          "توضیحات",
        ],
      ];
      console.log(this.data.service[0])
      
      
      for (let i = 0; i < this.data.service.length; i++) {
        let alongs = "";
        for(let j = 0; j < this.data.service[i].along.length; j++) {
          if(j > 0) {
            alongs += " - "
          }
          alongs = alongs + this.along_item(this.data.service[i].along[j].along_id)
        }
        tableData.push([
          this.$route.params.name,
          this.data.service[i].date,
          this.convertToJalali(this.data.service[i].date),
          this.data.service[i].city != null ? this.data.service[i].city.name: "",
          this.data.service[i].company != null ? this.data.service[i].company.name: "",
          alongs,
          this.data.service[i].price,
          this.data.service[i].pay != 1 ? "پرداخت نشده" : "پرداخت شده",
          this.data.service[i].more,
        ]);
      }
      console.log(this.total)
      const ws = XLSX.utils.aoa_to_sheet(tableData);

      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, "Sheet 1");

      XLSX.writeFile(wb, this.$route.params.name + ".xlsx");
    },
    _pay() {
      let ok = true;
      if (this.pay.data.length > 0) {
        for (let i = 0; i < this.pay.data.length; i++) {
          const foundItem = this.data.service.find(
            (item) => item.id === this.pay.data[i]
          );

          if (foundItem.price == null) {
            ok = false;
            this.showText = true;
            this.errotText =
              "خطایی رخ داده است!! احتمالا هزینه یک یا چند سرویس تایید نشده است. لطفا پس از تایید مجددا تلاش فرمایید";
            setTimeout(() => {
              this.showText = false;
            }, 8000);
            break;
          } else {
            continue;
          }
        }
        if (ok == true) {
          this.update_pay();
        }
      } else {
        this.showText = true;
        this.errotText = "هیچ موردی انتخاب نشده است";
        setTimeout(() => {
          this.showText = false;
        }, 8000);
      }
    },
    async update_pay() {
      try {
        const response = await axios.post("/api/pay_service", {
          pay: this.pay["data"],
        });

        this.fetchData();
        this.showText = true;
        this.errotText = "تبدیل انجام شد";
        setTimeout(() => {
          this.showText = false;
        }, 8000);
      } catch (error) {
        console.error("Error sending POST request:", error);
      }
    },
    async _notpay() {
      if (this.pay.data.length > 0) {
        try {
          const response = await axios.post("/api/notpay_service", {
            pay: this.pay["data"],
          });

          this.fetchData();
          this.showText = true;
          this.errotText = "تبدیل انجام شد";
          setTimeout(() => {
            this.showText = false;
          }, 8000);
        } catch (error) {
          console.error("Error sending POST request:", error);
        }
      } else {
        this.showText = true;
        this.errotText = "هیچ موردی انتخاب نشده است";
        setTimeout(() => {
          this.showText = false;
        }, 8000);
      }
    },
    async filter() {
      this.form_filter.id = this.$route.params.id;
      await this.form_filter
        .post("/api/filter_services_admin")
        .then((response) => {
          this.data = response.data;
          this.closeModal();
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    format_price(price) {
      return new Intl.NumberFormat('en-US').format(price);
    },
    async fetchData() {
      this.total = 0;
      this.total_notpay = 0;
      await axios
        .get("/api/services_admin_today/" + this.$route.params.id)
        .then((response) => {
          this.data = response.data;
          for (let i = 0; i < this.data.service.length; i++) {
            if(this.data.service[i].price != null) {
              this.total = this.total + this.data.service[i].price
              if(this.data.service[i].pay != 1) {
                this.total_notpay = this.total_notpay + this.data.service[i].price
              }
            }
          }
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async conf_price() {
      const id = this.selectedRow.id;
      const { data } = await this.form_price.post(
        "/api/edit_service_price_admin/" + id
      );
      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
        this.closeModal();
      }
    },
    async create() {
      // Register the user.
      this.form.id = this.$route.params.id;
      const { data } = await this.form.post("/api/create_service_admin");
      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
      }
    },
    async deleteservice(id) {
      const { data } = await this.form.post("/api/delete_service/" + id);
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
        this.closeModal_delete();
      }
    },
  },
};

</script>

<style scoped>
/* استایل برای مدیا پرینت */
</style>
