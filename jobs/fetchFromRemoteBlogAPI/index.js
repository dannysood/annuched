/**
 * Triggered from a message on a Cloud Pub/Sub topic.
 *
 * @param {!Object} event Event payload.
 * @param {!Object} context Metadata for the event.
 */
const axios = require('axios').default;
exports.fetchFromRemoteBlogAPI = (event, context) => {
  return axios.post(`https://api.annuched.dannysood.com/api/v1/job/fetch-from-remote-blog-api`,{"key":$process.env.JOBS_PRESHARED_SECRET},{headers: {
    "Accept": "application/json",
    "Content-Type": "application/json",

}})
    .then((response) => console.log("*******SUCCESS:", response))
    .catch((error) => console.log("!!!!!!!FAILURE:", error))
};
