import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Completed from '@proconvey/ui/src/svgs/completed'
import Link from 'next/link'
import React from 'react'

type PropTypes = {
  propertyId: string
}


const PackComplete = ({ propertyId }: PropTypes) => {
  return (
    <Card>
      <div className="p-5 md:p-[3.875rem]">
        <div className="max-w-[37.5rem] flex flex-col items-center mx-auto text-center">
          <Completed className="max-w-full w-[25rem] h-auto mb-[2.625rem]" />

          <H1 className="mb-[1.875rem]">You have completed your conveyancing pack</H1>
          <p className="mb-[1.25rem]">Your pack is complete and has been signed ready for your conveyancer to process.</p>
          <p className="mb-[3.125rem]">Your conveyancer has been notified and will review your pack. Your conveyancer will be in touch shortly.</p>
          <Link href={`/properties/${propertyId}`}
          >
            <Button>Back to Overview</Button>
          </Link>
        </div>
      </div>
    </Card>
  )
}

export default PackComplete
