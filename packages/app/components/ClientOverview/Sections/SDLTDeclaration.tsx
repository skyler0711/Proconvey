import Table from '@proconvey/ui/src/components/Table'
import Skeleton from 'react-loading-skeleton'
import Card from '@proconvey/ui/src/components/Card'
import { H3 } from '@proconvey/ui/src/components/Headers'
import { CrossIcon, TickIcon } from '@proconvey/ui/src/icons'

type PropTypes = {
  details: any
  fetching: boolean
}

const SDLTDeclaration = ({
  fetching,
  details,
}: PropTypes) => {

  const resolveAnswer = (answerText: string) => {
    if (answerText?.toLowerCase() === 'yes') {
      return <TickIcon className="text-mint w-[1rem]" />
    }

    return <CrossIcon className="text-mull w-[0.625rem]" />
  }

  const resolveHigherOrLower = (answerText: string) => {
    if (answerText?.toLowerCase() === 'yes') {
      return <p>Higher</p>
    }

    return <p>Standard</p>
  }

  return (
    <>
      {
        (details.buyers ?? [].length > 0) && (details.sdlt ?? [].length > 0)
          ?
          <Card className="mt-5">
            <Card.Header>
              <div className="flex flex-col items-start justify-between gap-5 sm:items-center sm:flex-row">
                <H3>SDLT decleration</H3>
              </div>
            </Card.Header>
            <Card.Body padContent={false}>
              <Table>
                <Table.Head>
                  <Table.Row>
                    <Table.Cell as="th" className="!text-[0.75rem]">
                  Buyer
                    </Table.Cell>
                    <Table.Cell as="th" className="!text-[0.75rem]">
                  SDLT rate
                    </Table.Cell>
                    <Table.Cell as="th" className="!text-[0.75rem]">
                  First time buyer?
                    </Table.Cell>
                    <Table.Cell as="th" className="!text-[0.75rem]">
                  First time buyer relief?
                    </Table.Cell>
                  </Table.Row>
                </Table.Head>

                <Table.Body>
                  {
                    fetching &&
                <>
                  <Table.Row>
                    <Table.Cell><Skeleton width="60%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="50%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="15%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="10%" height={27} /></Table.Cell>
                  </Table.Row>
                  <Table.Row>
                    <Table.Cell><Skeleton width="60%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="50%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="15%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="10%" height={27} /></Table.Cell>
                  </Table.Row>
                  <Table.Row>
                    <Table.Cell><Skeleton width="60%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="50%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="15%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="10%" height={27} /></Table.Cell>
                  </Table.Row>
                </>
                  }
                  {
                    !fetching &&
                    details.buyers.map((buyer: any, index: number) => (
                      <Table.Row key={index}>
                        <Table.Cell>
                          {buyer.name}
                        </Table.Cell>

                        <Table.Cell>
                          {resolveHigherOrLower(details.sdlt[index]?.higher_or_lower)}
                        </Table.Cell>

                        <Table.Cell>
                          {resolveAnswer(details.sdlt[index]?.first_time_buyer)}
                        </Table.Cell>

                        <Table.Cell>
                          {resolveAnswer(details.sdlt[index]?.first_time_buyer_relief)}
                        </Table.Cell>
                      </Table.Row>
                    ))
                  }
                </Table.Body>
              </Table>
            </Card.Body>
          </Card>
          :
          null
      }
    </>
  )
}

export default SDLTDeclaration
