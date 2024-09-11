import store from "~/store";

export default (to, from, next) => {
  if (store.getters["auth/user"].role == 0) {
    next({ name: "home" });
  } else {
    next();
  }
};
