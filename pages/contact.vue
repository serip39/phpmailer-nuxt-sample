<template>
  <v-card>
    <v-container>
      <v-card-title>Contact Us</v-card-title>
      <v-form
        ref="form"
        v-model="valid"
      >
        <v-text-field
          v-model="name"
          :counter="10"
          :rules="nameRules"
          label="Name"
          required
        />
        <v-text-field
          v-model="email"
          :rules="emailRules"
          label="E-mail"
          required
        />
        <v-textarea
          v-model="contents"
          :rules="contentsRules"
          label="Contents"
          required
        />
      </v-form>
      <v-card-actions>
        <v-spacer />
        <v-btn color="primary" @click="submit">Submit</v-btn>
      </v-card-actions>
    </v-container>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      valid: true,
      name: '',
      nameRules: [
        v => !!v || 'Name is required',
        v => (v && v.length <= 10) || 'Name must be less than 10 characters',
      ],
      email: '',
      emailRules: [
        v => !!v || 'E-mail is required',
        v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
      ],
      contents: '',
      contentsRules: [
        v => !!v || 'Contents is required',
      ]
    }
  },

  methods: {
    submit() {
      if (!this.$refs.form.validate()) return
      const data = {
        name: this.name,
        email: this.email,
        contents: this.contents
      }
      this.$axios.post('./mail.php', data)
        .then(res => {
          console.log(res)
        })
        .catch(err => {
          console.log(err.response)
        })
    }
  }
}
</script>