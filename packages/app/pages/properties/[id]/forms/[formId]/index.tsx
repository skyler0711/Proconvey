import { useQuery } from 'urql'
import { graphql } from 'gql'
import { useRouter } from 'next/router'

export default function Form () {
  const router = useRouter()

  const [{ data: property }] = useQuery({
    query: graphql(`
      query getFormIDProperty ($id: ID!) {
        property(id: $id) {
          id
          my_progress {
            provided_answers {
              id
              value
              answer {
                id
                step {
                  id
                }
              }
            }
          }
          active_forms {
            id
            pivot {
              ... on ActiveFormsPivot {
                id
              }
            }
            sections {
              id
              steps {
                id
                answers {
                  id
                }

              }
            }
          }
        }
      }
    `),
    variables: {
      id: router.query.id as string,
    },
  })

  const section = property?.property?.active_forms.filter((form) => form.pivot?.id === router.query.formId)?.[0].sections?.[0]

  const stepId = section?.steps?.[0]?.id
  const sectionId = section?.id

  if (stepId && sectionId) {
    router.replace(`/properties/${router.query.id}/forms/${router.query.formId}/sections/${sectionId}/steps/${stepId}`)
  }
}
