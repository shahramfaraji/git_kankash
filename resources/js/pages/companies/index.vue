<template>
  <div class="row">
    <div class="col-lg-5 fixed">
      <card :title="$t('ثبت سازمان')">
        <form @submit.prevent="create" @keydown="form.onKeydown($event)">
          <!-- Name -->
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">{{
              $t("نام سازمان")
            }}</label>
            <div class="col-md-7">
              <input
                v-model="form.name"
                :class="{ 'is-invalid': form.errors.has('name') }"
                class="form-control"
                type="text"
                name="name"
              />
              <has-error :form="form" field="name" />
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
      </card>
    </div>
    <div class="col-lg-7">
      <card :title="$t('لیست سازمانها')">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">نام سازمان</th>
              <th scope="col">ویرایش</th>
              <th scope="col">حذف</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="companies.length == 0" class="iran">
              <td colspan="5" class="text-center">موردی یافت نشد</td>
            </tr>
            <tr v-for="(company, index) in companies" :key="company.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ company.name }}</td>
              <td>
                <router-link
                  :to="{ name: 'company_edit', params: { id: company.id } }"
                  class="dropdown-item ps-3 t_r"
                >
                  <font-awesome-icon
                    :icon="icons.faEdit"
                    :fixed-width="true"
                    class="text-warning"
                  />
                </router-link>
              </td>
              <td>
                <font-awesome-icon
                  :icon="icons.faTrash"
                  :fixed-width="true"
                  class="text-danger pointer-cursor"
                  @click="openModal(company)"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </card>
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
                آیا از حذف {{ selectedRow.name }} مطمعن هستید؟
              </h1>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeModal"
              >
                خیر
              </button>
              <button
                type="button"
                class="btn btn-danger"
                @click="deleteuser(selectedRow.id)"
              >
                بله ادامه بده
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
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
} from "@fortawesome/free-solid-svg-icons";

export default {
  components: {
    LoginWithGithub,
    FontAwesomeIcon,
  },

  middleware: "admin",

  metaInfo() {
    return { title: this.$t("سازمانها") };
  },

  data: () => ({
    icons: {
      faEdit,
      faTrash,
      faInfoCircle,
    },
    form: new Form({
      name: "",
    }),
    companies: [],
    mustVerifyEmail: false,
    modalVisible: false,
    selectedRow: null,
  }),
  mounted() {
    this.fetchCities();
  },
  methods: {
    openModal(item) {
      this.selectedRow = item;
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
    async fetchCities() {
      await axios
        .get("/api/all_company")
        .then((response) => {
          this.companies = response.data.com;
        })
        .catch((error) => {
          console.error("Error fetching users", error);
        });
    },
    async create() {
      // Register the user.
      const { data } = await this.form.post("/api/create_company");

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchCities();
      }
    },
    async deleteuser(id) {
      const { data } = await this.form.post("/api/delete_company/" + id);
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        this.fetchCities();
        this.closeModal();
      }
    },
  },
};
</script>
