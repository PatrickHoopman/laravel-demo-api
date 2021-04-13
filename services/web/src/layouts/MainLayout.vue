<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-toolbar-title> Quasar App </q-toolbar-title>

        <q-space />
        <q-separator dark vertical />
        <q-btn stretch flat label="Products" to="/" />
        <q-separator dark vertical />
        <q-btn
          stretch
          flat
          label="Product Management"
          to="/product-management"
        />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: "MainLayout",
  data() {
    return {
      searchArgument: "",
    };
  },
};
</script>
