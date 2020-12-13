<?php
    class Commission {
        private $id;
        private $createdDate;
        private $modifiedDate;
        private $deleted;
        private $name;
        private $startDate;
        private $dueDate;
        private $progress;
        private $paid;
        private $priority;
        private $expectedDays;
        private $description;

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param mixed $id
         */
        public function setId($id): void
        {
            $this->id = $id;
        }

        /**
         * @return mixed
         */
        public function getCreatedDate()
        {
            return $this->createdDate;
        }

        /**
         * @param mixed $createdDate
         */
        public function setCreatedDate($createdDate): void
        {
            $this->createdDate = $createdDate;
        }

        /**
         * @return mixed
         */
        public function getModifiedDate()
        {
            return $this->modifiedDate;
        }

        /**
         * @param mixed $modifiedDate
         */
        public function setModifiedDate($modifiedDate): void
        {
            $this->modifiedDate = $modifiedDate;
        }

        /**
         * @return mixed
         */
        public function getDeleted()
        {
            return $this->deleted;
        }

        /**
         * @param mixed $deleted
         */
        public function setDeleted($deleted): void
        {
            $this->deleted = $deleted;
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name): void
        {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getStartDate()
        {
            return $this->startDate;
        }

        /**
         * @param mixed $startDate
         */
        public function setStartDate($startDate): void
        {
            $this->startDate = $startDate;
        }

        /**
         * @return mixed
         */
        public function getProgress()
        {
            return $this->progress;
        }

        /**
         * @param mixed $progress
         */
        public function setProgress($progress): void
        {
            $this->progress = $progress;
        }

        /**
         * @return mixed
         */
        public function getPaid()
        {
            return $this->paid;
        }

        /**
         * @param mixed $paid
         */
        public function setPaid($paid): void
        {
            $this->paid = $paid;
        }

        /**
         * @return mixed
         */
        public function getPriority()
        {
            return $this->priority;
        }

        /**
         * @param mixed $priority
         */
        public function setPriority($priority): void
        {
            $this->priority = $priority;
        }

        /**
         * @return mixed
         */
        public function getExpectedDays()
        {
            return $this->expectedDays;
        }

        /**
         * @param mixed $expectedDays
         */
        public function setExpectedDays($expectedDays): void
        {
            $this->expectedDays = $expectedDays;
        }

        /**
         * @return mixed
         */
        public function getDescription()
        {
            return $this->description;
        }

        /**
         * @param mixed $description
         */
        public function setDescription($description): void
        {
            $this->description = $description;
        }

        /**
         * @return mixed
         */
        public function getDueDate()
        {
            return $this->dueDate;
        }

        /**
         * @param mixed $dueDate
         */
        public function setDueDate($dueDate): void
        {
            $this->dueDate = $dueDate;
        }
    }
?>