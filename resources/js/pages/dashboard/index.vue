<template>
  <div>
    <div class="row">
      <div class="col-md-12 mb-2">
        <button type="button" class="btn btn-primary" @click="filter_btn()">
          فیلتر
        </button>
        <button type="button" class="btn btn-outline-primary" v-print>
          پرینت
        </button>
      </div>
    </div>
    <card class="bg-white">
      <FullCalendar
        :options="calendarOptions"
        @eventClick="handleEventClick"
        :events="calendarOptions.events"
      />
    </card>
    <div class="col-lg-12 mt-3 mb-5 dashboard_print">
      <card :title="$t('سرویس های من')">
        <small>
          شما در صفحه به صورت پیش فرض سرویس 100 روز آخر را مشاهده میکنید اما
          میتوانید با فیلتر کردن تعداد بیشتری را مشاهده نمایید.
        </small>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col">تاریخ</th>
              <th scope="col">کاربر</th>
              <th scope="col">همراهان</th>
              <th scope="col">شهر</th>
              <th scope="col">سازمان</th>
              <th scope="col">هزینه</th>
              <th scope="col">بیشتر</th>
              <th scope="col">ویرایش</th>
              <th scope="col">حذف</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data.service_all" :key="item.id">
              <td :class="[item.price !=null ? 'text-success' : 'text-danger']">
                {{ convertToJalali(item.date) }}
              </td>
              <td>
                {{ item.user.user }}
              </td>
              <td>
                {{ item.along.length }}
              </td>
              <td>
                <span v-if="item.city != null">{{ item.city.name }}</span>
              </td>
              <td>
                <span v-if="item.company != null">{{ item.company.name }}</span>
              </td>
              <td :class="[item.pay != 1 ? 'text-danger' : 'text-success']">
                {{ item.price }}
              </td>
              <td
                :class="[item.price != null ? 'text-success' : 'text-danger']"
                class="pointer-cursor"
              >
                <font-awesome-icon
                  :icon="icons.faInfoCircle"
                  :fixed-width="true"
                  @click="openModal(item)"
                />
              </td>
              <td>
                <font-awesome-icon
                  :icon="icons.faEdit"
                  :fixed-width="true"
                  class="text-warning pointer-cursor"
                  @click="editEvent(item.id, item.user.id, item.user.user)"
                />
              </td>
              <td>
                <font-awesome-icon
                  :icon="icons.faTrash"
                  :fixed-width="true"
                  class="text-danger pointer-cursor"
                  @click="openModal_delete(item)"
                />
              </td>
            </tr>
            <tr
              v-if="data.service_all == 0 || data.service_all == undefined"
              class="iran"
            >
              <td colspan="8" class="text-center">موردی یافت نشد</td>
            </tr>
          </tbody>
        </table>
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
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                جزئیات سرویس {{ selectedRow.user.user }}
              </h1>
            </div>
            <div class="modal-body">
              <table class="table">
                <tbody>
                  <tr>
                    <th>تاریخ</th>
                    <td>{{ convertToJalali(selectedRow.date) }}</td>
                  </tr>
                  <tr>
                    <th>شهر</th>
                    <td>
                      <span v-if="selectedRow.city != null">{{
                        selectedRow.city.name
                      }}</span>
                    </td>
                  </tr>
                  <tr>
                    <th>سازمان</th>
                    <td>
                      <span v-if="selectedRow.company != null">{{
                        selectedRow.company.name
                      }}</span>
                    </td>
                  </tr>
                  <tr>
                    <th>همراهان</th>
                    <td>
                      <ol v-if="selectedRow.along.length > 0">
                        <li v-for="item in selectedRow.along" :key="item.id">
                          {{ along_item(item.along_id) }}
                        </li>
                      </ol>
                      <span v-else> همراه نداشته </span>
                    </td>
                  </tr>
                  <tr>
                    <th>وضعیت</th>
                    <td>
                      <span v-if="selectedRow.pay != 1" class="text-danger">
                        پرداخت نشده
                      </span>
                      <span v-else class="text-success"> پرداخت شده </span>
                    </td>
                  </tr>
                  <tr>
                    <th>هزینه</th>
                    <td>
                      <span v-if="selectedRow.price != null">
                        {{ selectedRow.price }}
                      </span>
                      <span v-else>
                        <form
                          @submit.prevent="conf_price"
                          @keydown="form_price.onKeydown($event)"
                        >
                          <div class="row">
                            <div class="col-md-9">
                              <input
                                v-model="form_price.price"
                                :class="{
                                  'is-invalid': form_price.errors.has('price'),
                                }"
                                class="form-control"
                                type="number"
                                name="price"
                              />
                              <has-error :form="form_price" field="price" />
                            </div>
                            <div class="col-md-3">
                              <!-- Submit Button -->
                              <v-button :loading="form_price.busy">
                                {{ $t("تایید") }}
                              </v-button>

                              <!-- GitHub Register Button -->
                              <login-with-github />
                            </div>
                          </div>
                        </form>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th>توضیحات</th>
                    <td>{{ selectedRow.more }}</td>
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
              <button
                type="button"
                class="btn btn-warning"
                @click="
                  editEvent(
                    selectedRow.id,
                    selectedRow.user.id,
                    selectedRow.user.user
                  )
                "
              >
                ویرایش
              </button>
              <button
                type="button"
                class="btn btn-danger"
                @click="deleteservice(selectedRow.id)"
              >
                حذف
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    <transition name="fade">
      <div
        v-if="modalVisible_create"
        class="modal fade show"
        style="display: block"
        tabindex="-1"
        role="dialog"
        @click="closeModalOutside_create"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                اضافه کردن سرویس
              </h1>
            </div>
            <div class="modal-body">
              <form @submit.prevent="create" @keydown="form.onKeydown($event)">
                <div class="mb-3 row">
                  <label class="col-md-3 col-form-label text-md-end">{{
                    $t("کاربر *")
                  }}</label>
                  <div class="col-md-7">
                    <select
                      v-model="form.id"
                      :class="{ 'is-invalid': form.errors.has('id') }"
                      class="form-control iran"
                      @change="change_user"
                    >
                      <option
                        v-for="item in data.user"
                        :key="item.id"
                        :value="item.id"
                        calss="iran"
                      >
                        <span class="iran">{{ item.user }}</span>
                      </option>
                    </select>
                    <has-error :form="form" field="id" />
                  </div>
                </div>

                <div class="mb-3 row">
                  <label class="col-md-3 col-form-label text-md-end">{{
                    $t("تاریخ *")
                  }}</label>
                  <div class="col-md-7">
                    <date-picker
                      v-model="form.date"
                      simple
                      auto-submit
                      :class="{ 'is-invalid': form.errors.has('date') }"
                      class="form-control"
                      disabled="disabled"
                    />
                    <has-error :form="form" field="date" />
                  </div>
                </div>

                <div class="mb-3 row">
                  <label class="col-md-3 col-form-label text-md-end">{{
                    $t("شهر*")
                  }}</label>
                  <div class="col-md-7">
                    <select
                      v-model="form.city"
                      :class="{ 'is-invalid': form.errors.has('city') }"
                      class="form-control iran"
                    >
                      <option
                        v-for="item in data.city"
                        :key="item.id"
                        :value="item.id"
                        calss="iran"
                      >
                        <span class="iran">{{ item.name }}</span>
                      </option>
                    </select>
                    <has-error :form="form" field="city" />
                  </div>
                </div>

                <div class="mb-3 row">
                  <label class="col-md-3 col-form-label text-md-end">{{
                    $t("سازمان")
                  }}</label>
                  <div class="col-md-7">
                    <select v-model="form.company" class="form-control">
                      <option calss="iran" :value="null">
                        <span class="iran"></span>
                      </option>
                      <option
                        v-for="item in data.company"
                        :key="item.id"
                        calss="iran"
                        :value="item.id"
                      >
                        <span class="iran">{{ item.name }}</span>
                      </option>
                    </select>
                    <has-error :form="form" field="company" />
                  </div>
                </div>

                <div class="mb-3 row">
                  <label class="col-md-3 col-form-label text-md-end">{{
                    $t("همراهان")
                  }}</label>
                  <div class="col-md-7">
                    <Select2
                      v-model="form.alongs"
                      :options="new_alongs"
                      :settings="{ multiple: true }"
                      class="select2_custom"
                    />
                    <has-error :form="form" field="alongs" />
                  </div>
                </div>

                <div class="mb-3 row">
                  <label class="col-md-3 col-form-label text-md-end">{{
                    $t("هزینه")
                  }}</label>
                  <div class="col-md-7">
                    <input
                      v-model="form.price"
                      :class="{ 'is-invalid': form.errors.has('price') }"
                      class="form-control"
                      type="number"
                      name="price"
                    />
                    <has-error :form="form" field="price" />
                  </div>
                </div>

                <div class="mb-3 row">
                  <label class="col-md-3 col-form-label text-md-end">{{
                    $t("توضیحات")
                  }}</label>
                  <div class="col-md-7">
                    <textarea
                      v-model="form.more"
                      class="form-control"
                      id="exampleFormControlTextarea1"
                      rows="3"
                    ></textarea>
                    <has-error :form="form" field="more" />
                  </div>
                </div>

                <div class="mb-3 row">
                  <div class="col-md-7 offset-md-3 d-flex">
                    <!-- Submit Button -->
                    <v-button :loading="form.busy">
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
                @click="closeModal_create"
              >
                بستن
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    <transition name="fade">
      <div
        v-if="modalVisible_delete"
        class="modal fade show"
        style="display: block"
        tabindex="-1"
        role="dialog"
        @click="closeModalOutside_delete"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                آیا از حذف این سرویس {{ selectedRow_delete.user.user }} مطمعن
                هستید؟
              </h1>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeModal_delete"
              >
                خیر
              </button>
              <button
                type="button"
                class="btn btn-danger"
                @click="deleteservice(selectedRow_delete.id)"
              >
                بله ادامه بده
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    <transition name="fade">
      <div
        v-if="modalVisible_filter"
        class="modal fade show"
        style="display: block"
        tabindex="-1"
        role="dialog"
        @click="closeModalOutside_filter"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                فیلتر کردن سرویس ها
              </h1>
            </div>
            <div class="modal-body">
              <form @submit.prevent="filter" @keydown="form.onKeydown($event)">
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
                    $t("کاربر")
                  }}</label>
                  <div class="col-md-8 mb-2">
                    <select
                      v-model="form_filter.id"
                      :class="{ 'is-invalid': form_filter.errors.has('id') }"
                      class="form-control iran"
                    >
                      <option
                        v-for="item in data.user"
                        :key="item.id"
                        :value="item.id"
                        calss="iran"
                      >
                        <span class="iran">{{ item.user }}</span>
                      </option>
                    </select>
                    <has-error :form="form_filter" field="id" />
                  </div>
                  <label class="col-md-4 mb-2 col-form-label text-md-end">{{
                    $t("بدون هزینه")
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
                    </div>
                    <has-error :form="form_filter" field="price" />
                  </div>
                </div>

                <div class="mb-3 row">
                  <div class="col-md-7 offset-md-3 d-flex">
                    <!-- Submit Button -->
                    <v-button :loading="form_filter.busy">
                      {{ $t("فیلتر") }}
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
                @click="closeModal_filter"
              >
                بستن
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import frLocale from "@fullcalendar/core/locales/fa";
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
import print from "vue-print-nb";

export default {
  middleware: "admin",
  components: {
    LoginWithGithub,
    FontAwesomeIcon,
    FullCalendar,
    Select2,
  },
  directives: {
    print,
  },
  metaInfo() {
    return { title: this.$t("داشبورد") };
  },

  data() {
    return {
      icons: {
        faEdit,
        faTrash,
        faInfoCircle,
        faPrint,
      },
      calendarOptions: {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        locale: "fa",
        direction: "rtl",
        firstDay: 6,
        isJalaali: true,
        editable: true,
        editable: false,
        eventLimit: true,
        events: [],
      },
      form_filter: new Form({
        id: null,
        price: null,
        start: null,
        end: null,
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
      new_alongs: [],
      x: [
        { id: 1, text: "م1" },
        { id: 2, text: "2" },
      ],
      form_price: new Form({
        price: null,
      }),
      data: [],
      modalVisible: false,
      selectedRow: null,
      modalVisible_delete: false,
      selectedRow_delete: null,
      modalVisible_create: false,
      selectedRow_create: null,
      modalVisible_filter: false,
    };
  },
  mounted() {
    this.fetchData();
    document.documentElement.style.setProperty("--primary-dashboard", 100000);
  },
  beforeDestroy() {
    document.documentElement.style.setProperty("--primary-dashboard", 0);
  },
  methods: {
    change_user() {
      this.new_alongs.splice(0, this.new_alongs.length);
      for (let i = 0; i < this.data.users_along.length; i++) {
        if (this.data.users_along[i].id != this.form.id) {
          this.new_alongs.push(this.data.users_along[i]);
        }
      }
    },
    along_item(id) {
      const foundUser = this.data.users_along.find((item) => item.id === id);
      return foundUser.text;
    },
    openModal(item) {
      this.selectedRow = item;
      this.modalVisible = true;
      this.form_price.price = item.city.price;
    },
    closeModal() {
      this.modalVisible = false;
    },
    closeModalOutside(event) {
      if (event.target === event.currentTarget) {
        this.closeModal();
      }
    },
    openModal_delete(item) {
      this.selectedRow_delete = item;
      this.modalVisible_delete = true;
    },
    closeModal_delete() {
      this.modalVisible_delete = false;
    },
    closeModalOutside_delete(event) {
      if (event.target === event.currentTarget) {
        this.closeModal_delete();
      }
    },
    openModal_create(item) {
      this.selectedRow_create = item;
      this.modalVisible_create = true;
    },
    closeModal_create() {
      this.modalVisible_create = false;
    },
    closeModalOutside_create(event) {
      if (event.target === event.currentTarget) {
        this.closeModal_create();
      }
    },
    openModal_filter() {
      this.modalVisible_filter = true;
    },
    closeModal_filter() {
      this.modalVisible_filter = false;
    },
    closeModalOutside_filter(event) {
      if (event.target === event.currentTarget) {
        this.closeModal_filter();
      }
    },
    convertToJalali(date) {
      const jalaliMoment = moment(date, "YYYY-MM-DD").locale("fa");
      let jalaliDate = jalaliMoment.format("YYYY-MM-DD");
      return jalaliDate;
    },
    async fetchData() {
      await axios
        .get("/api/services_all_user/")
        .then((response) => {
          this.data = response.data;
          this.calendarOptions.events = response.data.services;
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async fetchDataForEvent(id) {
      await axios
        .get("/api/services_all_user_event/" + id)
        .then((response) => {
          let selectevent = response.data.service;
          this.openModal(selectevent);
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
      const { data } = await this.form.post("/api/create_service_admin");
      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
        this.closeModal_create();
      }
    },
    editEvent(id, user_id, user_name) {
      this.$router.push({
        name: "service_edit",
        params: { id: id, userid: user_id, name: user_name },
      });
    },
    filter_btn() {
      this.openModal_filter();
    },
    async filter() {
      await this.form_filter
        .post("/api/filter_services_all")
        .then((response) => {
          this.data = response.data;
          this.calendarOptions.events = response.data.services;
          this.closeModal_filter();
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async deleteservice(id) {
      const { data } = await this.form.post("/api/delete_service/" + id);
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchData();
        this.closeModal_delete();
        this.closeModal();
      }
    },
    handleDateClick: function (arg) {
      // this.calendarOptions.events.push({
      //   title: "event 1",
      //   date: arg.dateStr,
      // });
      this.form.date = this.convertToJalali(arg.dateStr);
      this.openModal_create(arg);
    },
    handleEventClick(info) {
      // info مشخصات رویداد کلیک شده
      this.fetchDataForEvent(info.event._def.publicId);
      // اجرای کد یا عملیات دلخواه شما...
    },
  },
};
</script>
