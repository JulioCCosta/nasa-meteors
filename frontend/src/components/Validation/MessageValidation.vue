<template>  
  <v-dialog :value="value"  @input="$emit('input')" :max-width="options.width" :style="{ zIndex: options.zIndex }" @keydown.esc="cancel">
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">{{title}}</v-toolbar-title>
      </v-toolbar>
      <v-card-text v-show="!!message" class="pa-4">{{ message }}</v-card-text>
      <v-card-actions class="pt-0">
        <v-spacer></v-spacer>
        <v-btn color="grey" text @click.native="$emit('input')">OK</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>

export default {
  data: () => ({
   
    dialog:false,
    options: {
      color: 'warning',
      width: 290,
      zIndex: 200
    }
  }),
  props:['value','title','message'],

}
</script>