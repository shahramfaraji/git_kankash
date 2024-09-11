function page(path) {
  return () =>
    import(/* webpackChunkName: '' */ `~/pages/${path}`).then(
      (m) => m.default || m
    );
}

export default [
  { path: "/", name: "login", component: page("auth/login.vue") },
  { path: "/register", name: "register", component: page("auth/register.vue") },
  {
    path: "/password/reset",
    name: "password.request",
    component: page("auth/password/email.vue"),
  },
  {
    path: "/password/reset/:token",
    name: "password.reset",
    component: page("auth/password/reset.vue"),
  },
  {
    path: "/email/verify/:id",
    name: "verification.verify",
    component: page("auth/verification/verify.vue"),
  },
  {
    path: "/email/resend",
    name: "verification.resend",
    component: page("auth/verification/resend.vue"),
  },

  { path: "/home", name: "home", component: page("home.vue") },
  // { path: "/home1", name: "home1", component: page("home1.vue") },
  { path: "/users", name: "users", component: page("users/index.vue") },
  {
    path: "/users/edit/:id",
    name: "users_edit",
    component: page("users/edit.vue"),
  },
  {
    path: "/users/more/:id/:name",
    name: "users_more",
    component: page("users/more.vue"),
  },
  {
    path: "/users/more/print/:id/:name",
    name: "users_more_print",
    component: page("users/print.vue"),
  },
  {
    path: "/users/service_edit/:id/:userid/:name",
    name: "service_edit",
    component: page("users/service_edit.vue"),
  },
  {
    path: "/users/mission_edit/:id/:userid/:name",
    name: "mission_edit",
    component: page("users/mission_edit.vue"),
  },
  { path: "/cities", name: "cities", component: page("cities/index.vue") },
  {
    path: "/cities/edit/:id",
    name: "city_edit",
    component: page("cities/edit.vue"),
  },
  {
    path: "/companies",
    name: "companies",
    component: page("companies/index.vue"),
  },
  {
    path: "/companies/edit/:id",
    name: "company_edit",
    component: page("companies/edit.vue"),
  },
  {
    path: "/dashboard",
    name: "dashboard",
    component: page("dashboard/index.vue"),
  },
  {
    path: "/settings",
    component: page("settings/index.vue"),
    children: [
      { path: "", redirect: { name: "settings.profile" } },
      {
        path: "profile",
        name: "settings.profile",
        component: page("settings/profile.vue"),
      },
      {
        path: "password",
        name: "settings.password",
        component: page("settings/password.vue"),
      },
    ],
  },

  { path: "*", component: page("errors/404.vue") },
];
