<template>
  <q-layout view="lHh Lpr lFf">
    <q-header class="loginHeader" elevated>
      <h4 class="absolute-bottom absolute-center" style="margin-left: 15px">
        BNB Bank
      </h4>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'LoginLayout',
});
</script>

<style lang="scss" scoped>
.loginHeader {
  min-height: 25%;
}
</style>
